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
            if doc_content.get('metadata', {}).get('saved_path'):
                if os.path.exists(doc_content['metadata']['saved_path']):
                    os.remove(doc_content['metadata']['saved_path'])
            
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

# Endpoint untuk menyajikan gambar (tidak perlu diubah)
@app.route('/images/<path:filename>')
def serve_image(filename):
    return send_from_directory(app.config['IMAGE_FOLDER'], filename)

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000, debug=True)