import os
import json
import logging
import hashlib
import numpy as np
import mysql.connector
from utils_py.utils import split_and_fix_sentences
from dotenv import load_dotenv
from image_similarity_py.image_similarity import ImageSimilarityModel
from utils_py.needleman_wunsch import needleman_wunsch_similarity
from utils_py.improved_similarity import enhanced_plagiarism_detection



logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")
load_dotenv()

class DatabaseManager:
    def __init__(self):
        try:
            self.connection = mysql.connector.connect(
                host=os.getenv('DB_HOST', 'localhost'),
                user=os.getenv('DB_USERNAME', 'root'),
                password=os.getenv('DB_PASSWORD', ''),
                database=os.getenv('DB_DATABASE', 'plagiarism_db')
            )
            self.cursor = self.connection.cursor(dictionary=True)
            logging.info("Database connection established successfully")

            self.image_folder = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'extract_data_py', 'images')

            self.image_model = ImageSimilarityModel()

        except mysql.connector.Error as err:
            logging.error(f"Error connecting to database: {err}")
            raise

    def __del__(self):
        if hasattr(self, 'cursor') and self.cursor: self.cursor.close()
        if hasattr(self, 'connection') and self.connection: self.connection.close()

    def check_and_save_document_hash(self, texts_full):
        document_hash = self._hash_text(''.join([page['text'] for page in texts_full]))
        self.cursor.execute("SELECT id FROM document_hashes WHERE doc_hash = %s", (document_hash,))
        result = self.cursor.fetchone()
        if result: return False, result['id']
        self.cursor.execute("INSERT INTO document_hashes (doc_hash) VALUES (%s)", (document_hash,))
        self.connection.commit()
        return True, self.cursor.lastrowid

    def get_document_metadata(self, doc_id):
        self.cursor.execute("SELECT d.title, d.author, dh.created_at FROM document_hashes dh LEFT JOIN documents d ON dh.id = d.doc_id WHERE dh.id = %s", (doc_id,))
        result = self.cursor.fetchone()
        if result and result.get('created_at'): result['created_at'] = result['created_at'].strftime('%Y-%m-%d %H:%M:%S')
        return result

    def store_document(self, doc_id, texts_full, tables, images, metadata=None):
        try:
            title = metadata.get('title') if metadata.get('title') else None
            author = metadata.get('author') if metadata.get('author') else None
            created_date = metadata.get('created')
            modified_date = metadata.get('modified')

            self.cursor.execute(
                "INSERT INTO documents (doc_id, title, author, created_date, modified_date) VALUES (%s, %s, %s, %s, %s)",
                (doc_id, title, author, created_date, modified_date)
            )
            main_document_id = self.cursor.lastrowid

            for text_block in texts_full:
                self.cursor.execute("INSERT INTO document_texts (document_id, page_number, text) VALUES (%s, %s, %s)",
                                    (main_document_id, text_block['page'], text_block['text']))

            for table_data in tables:
                self.cursor.execute("INSERT INTO document_tables (document_id, table_index, content) VALUES (%s, %s, %s)",
                                    (main_document_id, table_data['table_index'], json.dumps(table_data['content'])))

            for image_data in images:
                self.cursor.execute("INSERT INTO document_images (document_id, path, original_name, timestamp) VALUES (%s, %s, %s, %s)",
                                    (main_document_id, image_data['path'], image_data['name'], image_data['timestamp']))

            for page in texts_full:
                sentences = split_and_fix_sentences(page['text'])
                for sentence in sentences:
                    if sentence.strip():
                        hash_value = self._hash_text(self._normalize_text(sentence))
                        self.cursor.execute("INSERT IGNORE INTO sentence_hashes (hash_value, text) VALUES (%s, %s)", (hash_value, sentence.strip()))

                        hash_id = self.cursor.lastrowid
                        if hash_id == 0:
                            self.cursor.execute("SELECT id FROM sentence_hashes WHERE hash_value = %s", (hash_value,))
                            result = self.cursor.fetchone()
                            if result:
                                hash_id = result['id']

                        if hash_id:
                            self.cursor.execute("INSERT IGNORE INTO hash_locations (hash_id, doc_id, page_number) VALUES (%s, %s, %s)", (hash_id, doc_id, page['page']))

            for image_data in images:
                image_name = image_data.get('path')
                full_image_path = os.path.join(self.image_folder, image_name)
                if image_name and os.path.exists(full_image_path):
                    embedding = self.image_model.get_embedding(full_image_path)
                    if embedding is not None:
                        embedding_bytes = embedding.tobytes()
                        self.cursor.execute("INSERT INTO image_embeddings (doc_id, image_name, embedding) VALUES (%s, %s, %s)", (doc_id, image_name, embedding_bytes))

            self.connection.commit()
            logging.info(f"Document {doc_id} stored successfully.")
        except mysql.connector.Error as err:
            logging.error(f"Error storing document: {err}", exc_info=True)
            self.connection.rollback()
            raise

    def find_similar_images(self, doc_id, similarity_threshold=0.85):
        try:
            self.cursor.execute("SELECT image_name, embedding FROM image_embeddings WHERE doc_id = %s", (doc_id,))
            source_images = self.cursor.fetchall()

            self.cursor.execute("SELECT COUNT(id) FROM document_hashes")
            doc_count = self.cursor.fetchone()['COUNT(id)']

            query = """
                SELECT ie.doc_id, d.title as doc_title, ie.image_name, ie.embedding
                FROM image_embeddings ie
                LEFT JOIN documents d ON ie.doc_id = d.doc_id
            """
            params = ()

            if doc_count > 1:
                query += " WHERE ie.doc_id != %s"
                params = (doc_id,)

            self.cursor.execute(query, params)
            candidate_images = self.cursor.fetchall()

            logging.info(f"Source images: {len(source_images)}, Candidate images: {len(candidate_images)}")

            similar_images_report = []
            if not source_images or not candidate_images:
                return similar_images_report

            for cand in candidate_images:
                cand['embedding_np'] = np.frombuffer(cand['embedding'], dtype=np.float32)

            for index, source_img in enumerate(source_images):
                source_embedding = np.frombuffer(source_img['embedding'], dtype=np.float32)

                best_match_for_this_source_image = None
                max_similarity_for_this_source_image = -1.0

                for candidate_img in candidate_images:
                    dot_product = np.dot(source_embedding, candidate_img['embedding_np'])
                    norm_source = np.linalg.norm(source_embedding)
                    norm_candidate = np.linalg.norm(candidate_img['embedding_np'])
                    similarity = (dot_product / (norm_source * norm_candidate)) if norm_source > 0 and norm_candidate > 0 else 0

                    if similarity > max_similarity_for_this_source_image:
                        max_similarity_for_this_source_image = similarity
                        best_match_for_this_source_image = candidate_img

                if best_match_for_this_source_image and max_similarity_for_this_source_image >= similarity_threshold:

                    match_title = best_match_for_this_source_image['doc_title']
                    if not match_title:
                        match_title = f"Dokumen Tanpa Judul (ID: {best_match_for_this_source_image['doc_id']})"

                    is_only_match_with_self = all(c['doc_id'] == doc_id for c in candidate_images)

                    if source_img['image_name'] == best_match_for_this_source_image['image_name'] and not is_only_match_with_self:
                        continue

                    logging.info(f"REPORTING MATCH for source image index {index} with score {max_similarity_for_this_source_image:.4f}")
                    similar_images_report.append({
                        'source_image_index': index,
                        'source_image': source_img['image_name'],
                        'match_image': best_match_for_this_source_image['image_name'],
                        'match_doc_title': match_title,
                        'similarity': float(max_similarity_for_this_source_image)
                    })

            logging.info(f"Image plagiarism check finished. Found {len(similar_images_report)} similar images to report.")
            return similar_images_report

        except Exception as e:
            logging.error(f"An unexpected error in find_similar_images: {e}", exc_info=True)
            raise

    def calculate_plagiarism(self, doc_id):
        import time
        start_time = time.time()
        
        try:
            # Ambil kalimat dari dokumen yang sedang dicek
            self.cursor.execute("""
                SELECT hl.page_number, sh.text
                FROM hash_locations hl
                JOIN sentence_hashes sh ON hl.hash_id = sh.id
                WHERE hl.doc_id = %s
                ORDER BY hl.page_number, hl.id
            """, (doc_id,))
            current_sentences = self.cursor.fetchall()
            # (Opsional) Batasi hanya 200 kalimat awal jika dokumen sangat besar
            current_sentences = current_sentences[:200]
            
            logging.info(f"Processing {len(current_sentences)} sentences for doc_id: {doc_id}")

            # Ambil kalimat dari dokumen lain (optimized: lebih sedikit dan lebih relevan)
            self.cursor.execute("""
                SELECT hl.doc_id, hl.page_number, sh.text, d.title
                FROM hash_locations hl
                JOIN sentence_hashes sh ON hl.hash_id = sh.id
                JOIN documents d ON hl.doc_id = d.doc_id
                WHERE hl.doc_id != %s 
                AND LENGTH(sh.text) BETWEEN 20 AND 300
                ORDER BY d.created_at DESC
                LIMIT 2000
            """, (doc_id,))
            other_sentences = self.cursor.fetchall()

            total_sentences = len(current_sentences)
            plagiarized_sentences = []

            for row in current_sentences:
                current_text = self._normalize_text(row['text'])
                # Lewati kalimat yang terlalu pendek atau terlalu panjang
                if len(current_text) < 20 or len(current_text) > 300:
                    continue  # skip kalimat yang terlalu pendek atau panjang
                page_number = row['page_number']

                other_locations = []
                is_plagiarized = False
                best_score = 0.0
                best_method = "unknown"

                for other in other_sentences:
                    other_text = self._normalize_text(other['text'])
                    
                    # Use enhanced plagiarism detection with multiple similarity measures
                    result = enhanced_plagiarism_detection(current_text, other_text, threshold=0.75)
                    
                    if result['is_plagiarized']:
                        is_plagiarized = True
                        similarity_score = result['max_similarity']
                        
                        # Keep track of best score for this sentence
                        if similarity_score > best_score:
                            best_score = similarity_score
                            best_method = self._get_best_method(result['scores'])
                        
                        other_locations.append({
                            'doc_id': other['doc_id'],
                            'title': other['title'],
                            'page': other['page_number'],
                            'similarity_score': round(similarity_score, 4),
                            'detection_method': best_method,
                            'detailed_scores': {
                                'word': round(result['scores']['word_similarity'], 4),
                                'character': round(result['scores']['character_similarity'], 4),
                                'sequence': round(result['scores']['sequence_similarity'], 4),
                                'hybrid': round(result['scores']['hybrid_similarity'], 4)
                            }
                        })
                        
                        # Stop if we find a very strong match (>0.90)
                        if similarity_score > 0.90:
                            break
                        
                        # Limit matches per sentence to avoid too many results
                        if len(other_locations) >= 5:
                            break

                if is_plagiarized:
                    plagiarized_sentences.append({
                        'text': row['text'],
                        'matches': other_locations,
                        'best_similarity': round(best_score, 4),
                        'detection_method': best_method
                    })

            similarity_percentage = round((len(plagiarized_sentences) / total_sentences * 100), 2) if total_sentences > 0 else 0
            
            # Performance logging
            end_time = time.time()
            processing_time = end_time - start_time
            logging.info(f"Plagiarism calculation completed in {processing_time:.2f} seconds")
            logging.info(f"Found {len(plagiarized_sentences)} plagiarized sentences out of {total_sentences} total")
            
            return total_sentences, plagiarized_sentences, similarity_percentage

        except mysql.connector.Error as err:
            logging.error(f"Error calculating plagiarism: {err}")
            raise



    def _normalize_text(self, text):
        text = text.lower()
        text = ' '.join(text.split())
        text = ''.join(c for c in text if c.isalnum() or c.isspace())
        return text

    def _get_best_method(self, scores):
        """
        Determine which similarity method gave the highest score
        """
        max_score = 0
        best_method = "character"
        
        for method, score in scores.items():
            if score > max_score:
                max_score = score
                if method == 'word_similarity':
                    best_method = "word"
                elif method == 'sequence_similarity':
                    best_method = "sequence"
                elif method == 'hybrid_similarity':
                    best_method = "hybrid"
                else:
                    best_method = "character"
        
        return best_method

    def _hash_text(self, text):
        return hashlib.sha256(text.encode('utf-8')).hexdigest()

if __name__ == "__main__":
    # Contoh penggunaan
    db = DatabaseManager()

