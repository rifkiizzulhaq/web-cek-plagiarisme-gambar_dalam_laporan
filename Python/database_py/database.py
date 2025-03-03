import mysql.connector
import hashlib
import os
from PIL import Image
import io
from datetime import datetime
import json
import logging
from utils.utils import split_and_fix_sentences
from dotenv import load_dotenv

# Konfigurasi logging
logging.basicConfig(level=logging.INFO, format="%(asctime)s - %(levelname)s - %(message)s")

# Load environment variables dari file .env Laravel
load_dotenv()

class DatabaseManager:
    def __init__(self):
        """
        Inisialisasi koneksi database menggunakan konfigurasi dari .env Laravel
        """
        try:
            self.connection = mysql.connector.connect(
                host=os.getenv('DB_HOST', 'localhost'),
                user=os.getenv('DB_USERNAME', 'root'),
                password=os.getenv('DB_PASSWORD', ''),
                database=os.getenv('DB_DATABASE', 'myskripsi')
            )
            self.cursor = self.connection.cursor(dictionary=True)
            logging.info("Database connection established successfully")
            
            # Inisialisasi tabel-tabel yang diperlukan
            self.initialize_database()
            
        except mysql.connector.Error as err:
            logging.error(f"Error connecting to database: {err}")
            raise

    def __del__(self):
        """
        Cleanup koneksi database saat objek dihapus
        """
        if hasattr(self, 'cursor') and self.cursor:
            self.cursor.close()
        if hasattr(self, 'connection') and self.connection:
            self.connection.close()

    def initialize_database(self):
        """
        Membuat tabel-tabel yang diperlukan jika belum ada
        """
        try:
            # Tabel untuk menyimpan hash dokumen lengkap
            self.cursor.execute("""
                CREATE TABLE IF NOT EXISTS document_hashes (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    doc_hash VARCHAR(64) UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB
            """)

            # Tabel untuk menyimpan hash kalimat
            self.cursor.execute("""
                CREATE TABLE IF NOT EXISTS sentence_hashes (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    hash_value VARCHAR(64),
                    text TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_hash (hash_value)
                ) ENGINE=InnoDB
            """)

            # Tabel untuk menyimpan lokasi kemunculan hash
            self.cursor.execute("""
                CREATE TABLE IF NOT EXISTS hash_locations (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    hash_id BIGINT,
                    doc_id BIGINT,
                    page_number INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (hash_id) REFERENCES sentence_hashes(id) ON DELETE CASCADE,
                    FOREIGN KEY (doc_id) REFERENCES document_hashes(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_location (hash_id, doc_id, page_number)
                ) ENGINE=InnoDB
            """)

            # Tabel untuk menyimpan metadata dokumen
            self.cursor.execute("""
                CREATE TABLE IF NOT EXISTS documents (
                    id BIGINT AUTO_INCREMENT PRIMARY KEY,
                    doc_id BIGINT,
                    title VARCHAR(255),
                    author VARCHAR(255),
                    created_date TIMESTAMP NULL,
                    modified_date TIMESTAMP NULL,
                    text_content LONGTEXT,
                    tables_json LONGTEXT,
                    images_json LONGTEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (doc_id) REFERENCES document_hashes(id) ON DELETE CASCADE
                ) ENGINE=InnoDB
            """)

            self.connection.commit()
            logging.info("Database tables initialized successfully")
        except mysql.connector.Error as err:
            logging.error(f"Error initializing database: {err}")
            self.connection.rollback()
            raise

    def check_and_save_document_hash(self, texts_full):
        """
        Memeriksa dan menyimpan hash dokumen lengkap
        """
        document_hash = self._hash_text(''.join([page['text'] for page in texts_full]))
        
        try:
            # Cek apakah hash sudah ada
            self.cursor.execute(
                "SELECT id FROM document_hashes WHERE doc_hash = %s",
                (document_hash,)
            )
            result = self.cursor.fetchone()
            
            if result:
                return False, result['id']
            
            # Simpan hash baru
            self.cursor.execute(
                "INSERT INTO document_hashes (doc_hash) VALUES (%s)",
                (document_hash,)
            )
            self.connection.commit()
            return True, self.cursor.lastrowid
            
        except mysql.connector.Error as err:
            logging.error(f"Error checking/saving document hash: {err}")
            self.connection.rollback()
            raise

    def store_document(self, doc_id, texts_full, tables, images, metadata=None):
        """
        Menyimpan dokumen baru ke database dengan metadata lengkap
        """
        try:
            # Simpan metadata dokumen
            metadata_values = []
            metadata_fields = []
            
            if metadata:
                if metadata.get('title'):
                    metadata_fields.append('title')
                    metadata_values.append(metadata.get('title'))
                if metadata.get('author'):
                    metadata_fields.append('author')
                    metadata_values.append(metadata.get('author'))
                if metadata.get('created'):
                    metadata_fields.append('created_date')
                    metadata_values.append(metadata.get('created'))
                if metadata.get('modified'):
                    metadata_fields.append('modified_date')
                    metadata_values.append(metadata.get('modified'))
            
            # Buat query dinamis berdasarkan metadata yang tersedia
            base_fields = ['doc_id', 'text_content', 'tables_json', 'images_json']
            all_fields = base_fields + metadata_fields
            
            placeholders = ', '.join(['%s'] * len(all_fields))
            fields_str = ', '.join(all_fields)
            update_str = ', '.join(f"{field} = VALUES({field})" for field in all_fields)
            
            query = f"""
                INSERT INTO documents ({fields_str})
                VALUES ({placeholders})
                ON DUPLICATE KEY UPDATE {update_str}
            """
            
            # Siapkan values untuk query
            base_values = [
                doc_id,
                json.dumps([{'page': page['page'], 'text': page['text']} for page in texts_full]),
                json.dumps(tables),
                json.dumps(images)
            ]
            all_values = base_values + metadata_values
            
            self.cursor.execute(query, all_values)
            
            # Proses dan simpan hash kalimat
            processed_sentences = {}  # Menggunakan dict untuk melacak halaman
            
            for page in texts_full:
                page_number = page['page']
                sentences = split_and_fix_sentences(page['text'])
                for sentence in sentences:
                    sentence = sentence.strip()
                    if sentence:
                        # Gunakan kalimat asli sebagai kunci untuk melacak halaman
                        if sentence not in processed_sentences:
                            processed_sentences[sentence] = page_number
                            
                            # Normalisasi teks untuk hashing
                            normalized_text = self._normalize_text(sentence)
                            hash_value = self._hash_text(normalized_text)
                            
                            # Simpan hash kalimat jika belum ada
                            self.cursor.execute("""
                                INSERT IGNORE INTO sentence_hashes (hash_value, text)
                                VALUES (%s, %s)
                            """, (hash_value, sentence))
                            
                            # Dapatkan ID hash
                            self.cursor.execute(
                                "SELECT id FROM sentence_hashes WHERE hash_value = %s",
                                (hash_value,)
                            )
                            hash_id = self.cursor.fetchone()['id']
                            
                            # Simpan lokasi hash
                            self.cursor.execute("""
                                INSERT IGNORE INTO hash_locations (hash_id, doc_id, page_number)
                                VALUES (%s, %s, %s)
                            """, (hash_id, doc_id, page_number))

            self.connection.commit()
            logging.info(f"Document {doc_id} stored successfully with {len(processed_sentences)} unique sentences")
            
        except mysql.connector.Error as err:
            logging.error(f"Error storing document: {err}")
            self.connection.rollback()
            raise

    def calculate_plagiarism(self, doc_id, output_file="output_citations.html"):
        """
        Menghitung plagiarisme dan menghasilkan laporan
        """
        try:
            logging.info(f"Calculating plagiarism for document ID: {doc_id}")
            
            # Query untuk membandingkan kalimat dengan dokumen lain
            self.cursor.execute("""
                WITH current_doc_sentences AS (
                    SELECT DISTINCT 
                        sh.id as sentence_id,
                        sh.text as sentence_text,
                        sh.hash_value,
                        hl.page_number
                    FROM hash_locations hl
                    JOIN sentence_hashes sh ON hl.hash_id = sh.id
                    WHERE hl.doc_id = %s
                ),
                matching_sentences AS (
                    SELECT 
                        cds.sentence_text,
                        cds.page_number as source_page,
                        hl2.doc_id as matching_doc_id,
                        hl2.page_number as matching_page,
                        d.title as matching_doc_title
                    FROM current_doc_sentences cds
                    JOIN hash_locations hl2 ON hl2.hash_id IN (
                        SELECT id 
                        FROM sentence_hashes 
                        WHERE hash_value = cds.hash_value
                    )
                    LEFT JOIN documents d ON hl2.doc_id = d.doc_id
                    WHERE hl2.doc_id != %s
                )
                SELECT 
                    cds.sentence_text as text,
                    cds.page_number,
                    COUNT(DISTINCT ms.matching_doc_id) as match_count,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            ms.matching_doc_id, ':',
                            COALESCE(ms.matching_doc_title, CONCAT('Document ', ms.matching_doc_id)), ':',
                            ms.matching_page
                        )
                        SEPARATOR '|'
                    ) as matching_docs
                FROM current_doc_sentences cds
                LEFT JOIN matching_sentences ms ON cds.sentence_text = ms.sentence_text
                GROUP BY cds.sentence_id, cds.sentence_text, cds.page_number
                ORDER BY cds.page_number, cds.sentence_id
            """, (doc_id, doc_id))
            
            results = self.cursor.fetchall()
            logging.info(f"Found {len(results)} sentences to analyze")
            
            total_sentences = len(results)
            plagiarized_sentences = []
            all_sentences = []
            
            for row in results:
                other_locations = []
                matching_docs_str = row.get('matching_docs')
                
                if matching_docs_str:
                    for doc_info in matching_docs_str.split('|'):
                        if doc_info:
                            parts = doc_info.split(':')
                            if len(parts) >= 3:
                                doc_id_str, title, page = parts[0], parts[1], parts[2]
                                other_locations.append({
                                    'doc_id': int(doc_id_str),
                                    'title': title,
                                    'page': int(page)
                                })
                
                is_plagiarized = len(other_locations) > 0
                
                if is_plagiarized:
                    plagiarized_sentences.append({
                        'text': row['text'],
                        'matches': other_locations
                    })
                    logging.info(f"Plagiarized sentence found: '{row['text'][:100]}...'")
                    logging.info(f"Matching documents: {other_locations}")
                
                all_sentences.append({
                    'text': row['text'],
                    'plagiarized': is_plagiarized,
                    'other_locations': other_locations,
                    'page': row['page_number']
                })
            
            plagiarism_percentage = round((len(plagiarized_sentences) / total_sentences * 100), 2) if total_sentences > 0 else 0
            logging.info(f"Plagiarism calculation completed. Total: {total_sentences}, Plagiarized: {len(plagiarized_sentences)}, Percentage: {plagiarism_percentage:.2f}%")
            
            # Generate HTML report
            self._generate_html_report(doc_id, all_sentences, plagiarism_percentage, output_file)
            
            return total_sentences, len(plagiarized_sentences), plagiarism_percentage
            
        except mysql.connector.Error as err:
            logging.error(f"Error calculating plagiarism: {err}")
            raise

    def _normalize_text(self, text):
        """
        Normalisasi teks untuk perbandingan yang lebih akurat
        """
        # Ubah ke lowercase
        text = text.lower()
        
        # Hapus karakter khusus dan whitespace berlebih
        text = ' '.join(text.split())
        
        # Hapus tanda baca
        text = ''.join(c for c in text if c.isalnum() or c.isspace())
        
        return text

    def _hash_text(self, text):
        """
        Menghasilkan hash dari teks yang sudah dinormalisasi
        """
        return hashlib.sha256(text.encode('utf-8')).hexdigest()

    def _generate_html_report(self, doc_id, sentences, plagiarism_percentage, output_file):
        """
        Menghasilkan laporan HTML dengan format yang lebih detail dan gambar sesuai struktur dokumen
        """
        # Ambil data dokumen lengkap
        self.cursor.execute("""
            SELECT d.images_json, d.text_content
            FROM documents d
            WHERE d.doc_id = %s
        """, (doc_id,))
        result = self.cursor.fetchone()
        
        if not result:
            logging.error(f"Document {doc_id} not found")
            return
            
        images = json.loads(result['images_json']) if result['images_json'] else []
        text_content = json.loads(result['text_content']) if result['text_content'] else []

        html_content = f"""
        <html>
        <head>
            <title>Similarity Report for Document {doc_id}</title>
            <style>
                body {{ font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }}
                h1, h2 {{ color: #2C3E50; }}
                .highlight {{ background-color: #fff3cd; padding: 2px 5px; border-radius: 3px; }}
                .match-info {{ font-size: 0.9em; color: #666; margin-left: 10px; }}
                .match-source {{ background-color: #e9ecef; padding: 2px 6px; border-radius: 3px; margin-right: 5px; }}
                .sentence {{ margin-bottom: 15px; }}
                .page-number {{ color: #666; font-size: 0.8em; margin-right: 10px; }}
                .image-container {{ margin: 20px 0; text-align: center; }}
                .document-image {{ max-width: 80%; height: auto; margin: 10px auto; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }}
                .image-caption {{ color: #666; font-style: italic; margin-top: 5px; text-align: center; }}
                .section {{ margin: 20px 0; }}
                .heading {{ color: #2C3E50; margin: 15px 0; }}
            </style>
        </head>
        <body>
            <h1>Similarity Report</h1>
            <div class="section">
                <h2>Summary</h2>
                <p><b>Document ID:</b> {doc_id}</p>
                <p><b>Total Sentences:</b> {len(sentences)}</p>
                <p><b>Plagiarized Sentences:</b> {sum(1 for s in sentences if s['plagiarized'])}</p>
                <p><b>Similarity Percentage:</b> {plagiarism_percentage:.2f}%</p>
            </div>
        """

        # Mulai analisis dokumen
        html_content += "<div class='section'><h2>Document Analysis</h2>"
        
        current_page = None
        image_index = 0
        
        for sentence in sentences:
            # Jika pindah ke halaman baru
            if current_page != sentence['page']:
                current_page = sentence['page']
                html_content += f'<div class="page-break"><hr><h3 class="heading">Page {current_page}</h3></div>'
            
            # Tambahkan kalimat
            html_content += '<div class="sentence">'
            if sentence['plagiarized']:
                match_info = []
                for loc in sentence['other_locations']:
                    match_info.append(
                        f'<span class="match-source">{loc["title"]} (Page {loc["page"]})</span>'
                    )
                html_content += f'''
                    <span class="highlight">{sentence["text"]}</span>
                    <span class="match-info">Found in: {" ".join(match_info)}</span>
                '''
            else:
                html_content += f'<span>{sentence["text"]}</span>'
            html_content += '</div>'
            
            # Cek apakah ada gambar yang seharusnya ditampilkan di halaman ini
            while image_index < len(images):
                image = images[image_index]
                image_page = image.get('page', 0)
                
                if image_page == current_page:
                    image_path = image.get('path', '')
                    caption = image.get('caption', '')
                    if image_path:
                        # Konversi path relatif ke URL yang benar
                        image_url = f"/storage/{image_path}" if not image_path.startswith('/') else image_path
                        html_content += f"""
                        <div class="image-container">
                            <img src="{image_url}" alt="{caption}" class="document-image">
                            <div class="image-caption">{caption}</div>
                        </div>
                        """
                    image_index += 1
                else:
                    break

        html_content += """
        </div></body>
        </html>
        """

        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(html_content)
        
        logging.info(f"Report generated: {output_file}")

    def delete_document(self, doc_id):
        """
        Menghapus dokumen dan semua data terkait
        """
        try:
            # Hapus lokasi hash
            self.cursor.execute("""
                DELETE FROM hash_locations WHERE doc_id = %s
            """, (doc_id,))
            
            # Hapus hash yang tidak memiliki referensi
            self.cursor.execute("""
                DELETE sh FROM sentence_hashes sh
                LEFT JOIN hash_locations hl ON sh.id = hl.hash_id
                WHERE hl.id IS NULL
            """)
            
            # Hapus dokumen
            self.cursor.execute("""
                DELETE FROM documents WHERE id = %s
            """, (doc_id,))
            
            self.connection.commit()
            logging.info(f"Document {doc_id} deleted successfully")
            
        except mysql.connector.Error as err:
            logging.error(f"Error deleting document: {err}")
            self.connection.rollback()
            raise

if __name__ == "__main__":
    # Contoh penggunaan
    db = DatabaseManager()
    db.initialize_database()

