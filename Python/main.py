import os
import logging
from flask_cors import CORS
from datetime import datetime
from werkzeug.utils import secure_filename
from database_py.database import DatabaseManager
from docx_reader_py.docx_reader import DocxReader
from flask import Flask, request, jsonify, send_from_directory
from utils_py.utils import split_and_fix_sentences, preprocess_text

logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

app = Flask(__name__)
CORS(app, resources={r"/api/*": {"origins": "*"}}, expose_headers=["Content-Type", "X-Custom-Message-Header"])
app.config['UPLOAD_FOLDER'] = os.path.abspath('uploads_py')
app.config['IMAGE_FOLDER'] = os.path.join(os.path.abspath('extract_data_py'), 'images')

for folder in [app.config['UPLOAD_FOLDER'], app.config['IMAGE_FOLDER']]:
    os.makedirs(folder, exist_ok=True)

@app.route('/')
def index():
    return jsonify({'status': 'success', 'message': 'Plagiarism Checker API is running'})

@app.route('/api/check-plagiarism', methods=['POST'])
def check_plagiarism():
    file_path = None
    try:     
        os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)

        if 'file' not in request.files:
            return jsonify({'success': False, 'message': 'Tidak ada file yang dikirim'}), 400
        
        file = request.files['file']
        if not file.filename.lower().endswith('.docx'):
            return jsonify({'success': False, 'message': 'File harus berformat DOCX'}), 400
            
        filename = secure_filename(file.filename)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        saved_filename = f"{timestamp}_{filename}"
        file_path = os.path.join(app.config['UPLOAD_FOLDER'], saved_filename)
        file.save(file_path)
        
        db_manager = DatabaseManager()
        docx_reader = DocxReader(file_path)
        doc_content = docx_reader.process_document()
        
        if not doc_content or not doc_content.get('texts_full'):
            raise Exception("Tidak ada konten teks yang valid ditemukan di dalam dokumen")

        is_new, doc_id = db_manager.check_and_save_document_hash(doc_content['texts_full'])

        if not is_new:
            os.remove(file_path)
            for image_data in doc_content.get('images', []):
                extracted_image_path = os.path.join(app.config['IMAGE_FOLDER'], image_data.get('path', ''))
                if os.path.exists(extracted_image_path):
                    os.remove(extracted_image_path)
            original_doc = db_manager.get_document_metadata(doc_id)
            message = "Dokumen ini sudah pernah diunggah."
            if original_doc and original_doc.get('created_at'):
                 message = f"Dokumen ini sudah pernah diunggah pada {original_doc['created_at']}."
            
            return jsonify({'success': False, 'message': message}), 200

        db_manager.store_document(
            doc_id, doc_content['texts_full'], doc_content['tables'],
            doc_content['images'], doc_content['metadata']
        )

        total_sentences, plagiarized_sentences_list, similarity_percentage = (
            db_manager.calculate_plagiarism(doc_id)
        )
        
        similar_images = db_manager.find_similar_images(doc_id)
        
        total_images = len(doc_content.get('images', []))
        indicated_images = len(similar_images)
        
        os.remove(file_path)
        
        return jsonify({
            'success': True,
            'data': {
                'doc_id': doc_id,
                'total_sentences': total_sentences,
                'plagiarized_sentences': len(plagiarized_sentences_list),
                'similarity_percentage': similarity_percentage,
                'timestamp': timestamp, 
                'image_similarity_report': similar_images,
                'total_images': total_images,
                'indicated_images': indicated_images
            }
        }), 200
        
    except Exception as e:
        logging.error(f"API Error: {str(e)}", exc_info=True)
        if file_path and os.path.exists(file_path):
            os.remove(file_path)
        
        return jsonify({
            'success': False,
            'message': f'Terjadi kesalahan fatal di server Python: {str(e)}'
        }), 200

# Endpoint untuk mengambil hasil deteksi plagiarisme
@app.route('/api/get-result/<int:file_id>', methods=['GET'])
def get_result(file_id):
    try:
        db_manager = DatabaseManager()
        
        # Get plagiarism results from database
        total_sentences, plagiarized_sentences, similarity_percentage = db_manager.calculate_plagiarism(file_id)
        
        # Format sentences for frontend display with Flask-style citations
        formatted_sentences = []
        citation_counter = 1
        citation_map = {}
        
        # Get all sentences for this document with correct table structure
        db_manager.cursor.execute("""
            SELECT dt.text, dt.page_number 
            FROM documents d
            JOIN document_texts dt ON d.id = dt.document_id
            WHERE d.doc_id = %s 
            ORDER BY dt.page_number
        """, (file_id,))
        
        document_pages = db_manager.cursor.fetchall()
        
        # Split text into sentences for each page
        all_sentences = []
        for page in document_pages:
            sentences = split_and_fix_sentences(page['text'])
            for sentence in sentences:
                if sentence.strip():
                    all_sentences.append({
                        'text': sentence.strip(),
                        'page_number': page['page_number']
                    })
        
        for sentence in all_sentences:
            sentence_text = sentence['text']
            page_number = sentence['page_number']
            
            # Check if this sentence is plagiarized
            is_plagiarized = False
            other_locations = []
            best_similarity = 0
            citations = []
            
            for plag_sentence in plagiarized_sentences:
                if plag_sentence['text'] == sentence_text:
                    is_plagiarized = True
                    other_locations = plag_sentence['matches']
                    best_similarity = plag_sentence['best_similarity']
                    
                    # Create citations in Flask style
                    for match in other_locations:
                        citation_key = f"{match['doc_id']}_{match['page']}"
                        
                        if citation_key not in citation_map:
                            # Tangani jika judul dokumen bernilai null/kosong dari metadata Word
                            displayed_title = match.get('title')
                            if not displayed_title:
                                displayed_title = f"Dokumen Rujukan (ID: {match['doc_id']})"
                                
                            citation_map[citation_key] = {
                                'citation_number': citation_counter,
                                'source_doc_id': match['doc_id'],
                                'title': displayed_title,
                                'page': match['page'],
                                'similarity_score': match['similarity_score']
                            }
                            citation_counter += 1
                        
                        citations.append(citation_map[citation_key]['citation_number'])
                    
                    break
            
            formatted_sentence = {
                'text': sentence_text,
                'page': page_number,
                'is_plagiarized': is_plagiarized,
                'other_locations': other_locations,
                'best_similarity': best_similarity,
                'citations': citations  # Flask-style citation numbers [1], [2], etc.
            }
            
            formatted_sentences.append(formatted_sentence)
        
        # Convert citation_map to list for frontend
        citation_list = list(citation_map.values())
        
        return jsonify({
            'success': True,
            'data': {
                'total_sentences': total_sentences,
                'plagiarized_count': len(plagiarized_sentences),
                'similarity_percentage': similarity_percentage,
                'sentences': formatted_sentences,
                'citations': citation_list  # For citation modal
            }
        })
        
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500

# Endpoint untuk mendapatkan dokumen sumber untuk sitasi
@app.route('/api/get-source-document/<int:doc_id>', methods=['GET'])
def get_source_document(doc_id):
    try:
        db_manager = DatabaseManager()
        
        # Get document metadata
        db_manager.cursor.execute("""
            SELECT d.title, d.author, d.created_date, d.modified_date, dh.created_at
            FROM document_hashes dh
            LEFT JOIN documents d ON dh.id = d.doc_id
            WHERE dh.id = %s
        """, (doc_id,))
        
        document = db_manager.cursor.fetchone()
        
        if not document:
            return jsonify({'success': False, 'message': 'Dokumen tidak ditemukan'}), 404
        
        # Get document text content
        db_manager.cursor.execute("""
            SELECT dt.page_number, dt.text
            FROM documents d
            JOIN document_texts dt ON d.id = dt.document_id
            WHERE d.doc_id = %s
            ORDER BY dt.page_number
        """, (doc_id,))
        
        text_pages = db_manager.cursor.fetchall()
        
        return jsonify({
            'success': True,
            'data': {
                'doc_id': doc_id,
                'title': document['title'] or f'Dokumen {doc_id}',
                'author': document['author'],
                'created_date': document['created_date'].isoformat() if document['created_date'] else None,
                'modified_date': document['modified_date'].isoformat() if document['modified_date'] else None,
                'uploaded_at': document['created_at'].isoformat() if document['created_at'] else None,
                'pages': [{'page': page['page_number'], 'text': page['text']} for page in text_pages]
            }
        })
        
    except Exception as e:
        logging.error(f"Error getting source document: {str(e)}")
        return jsonify({'success': False, 'message': str(e)}), 500

# Endpoint untuk menyajikan gambar (tidak perlu diubah)
@app.route('/images/<path:filename>')
def serve_image(filename):
    return send_from_directory(app.config['IMAGE_FOLDER'], filename)

# Endpoint untuk menghapus semua dokumen (untuk testing)
@app.route('/api/clear-documents', methods=['DELETE'])
def clear_documents():
    try:
        db_manager = DatabaseManager()
        
        # Delete all documents and related data
        tables_to_clear = [
            'document_images',
            'document_tables', 
            'document_texts',
            'documents',
            'document_hashes'
        ]
        
        for table in tables_to_clear:
            db_manager.cursor.execute(f"DELETE FROM {table}")
        
        db_manager.connection.commit()
        
        # Clear uploaded images
        import shutil
        image_folder = app.config['IMAGE_FOLDER']
        if os.path.exists(image_folder):
            shutil.rmtree(image_folder)
            os.makedirs(image_folder, exist_ok=True)
        
        return jsonify({
            'success': True,
            'message': 'Semua dokumen berhasil dihapus'
        }), 200
        
    except Exception as e:
        logging.error(f"Error clearing documents: {str(e)}")
        return jsonify({'success': False, 'message': str(e)}), 500

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000, debug=True)