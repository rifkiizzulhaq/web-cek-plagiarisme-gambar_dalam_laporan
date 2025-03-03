import os
import sys
import logging
from datetime import datetime
from flask import Flask, request, jsonify, send_from_directory, url_for
from werkzeug.utils import secure_filename
from utils.utils import (
    split_and_fix_sentences,
    preprocess_text,
    is_common_caption,
    is_heading
)
from database_py.database import DatabaseManager
from docx_reader.docx_reader import DocxReader

# Konfigurasi logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('plagiarism_check.log'),
        logging.StreamHandler(sys.stdout)
    ]
)

app = Flask(__name__)
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size
app.config['UPLOAD_FOLDER'] = os.path.abspath('uploads')
app.config['REPORT_FOLDER'] = os.path.abspath('reports')
app.config['EXTRACT_DATA_FOLDER'] = os.path.abspath('extract_data')
app.config['IMAGE_FOLDER'] = os.path.join(app.config['EXTRACT_DATA_FOLDER'], 'images')

# Ensure all required directories exist with proper permissions
for folder in [app.config['UPLOAD_FOLDER'], app.config['REPORT_FOLDER'], 
               app.config['EXTRACT_DATA_FOLDER'], app.config['IMAGE_FOLDER']]:
    try:
        os.makedirs(folder, exist_ok=True)
        logging.info(f"Directory ensured: {folder}")
    except Exception as e:
        logging.error(f"Error creating directory {folder}: {str(e)}")

@app.route('/')
def index():
    """
    Root endpoint untuk testing
    """
    return jsonify({
        'status': 'success',
        'message': 'Plagiarism Checker API is running',
        'endpoints': {
            'check_plagiarism': '/api/check-plagiarism',
            'get_report': '/api/reports/<filename>'
        }
    })

class PlagiarismChecker:
    def __init__(self, db_manager=None):
        """
        Inisialisasi PlagiarismChecker dengan DatabaseManager opsional
        """
        self.db_manager = db_manager or DatabaseManager()
        self.docx_reader = None
        
    def process_document(self, file_path):
        """
        Memproses dokumen DOCX dan mengekstrak kontennya
        """
        try:
            logging.info(f"Memproses dokumen: {file_path}")
            
            self.docx_reader = DocxReader(file_path)
            results = self.docx_reader.process_document()
            
            processed_text = []
            text_content = results.get('text', {}).get('content', [])
            current_page = 1
            
            for item in text_content:
                text = item.get('content', '')
                if isinstance(text, dict):
                    text = text.get('text', '')
                elif isinstance(text, str):
                    text = text
                
                if text and not is_common_caption(text):
                    sentences = split_and_fix_sentences(text)
                    for sentence in sentences:
                        if sentence.strip() and not is_heading(sentence):
                            # Normalisasi teks sebelum preprocessing
                            normalized = ' '.join(sentence.split())  # Menghapus whitespace berlebih
                            if len(normalized) > 10:  # Hanya proses kalimat yang cukup panjang
                                processed_text.append({
                                    'text': normalized,
                                    'preprocessed': preprocess_text(normalized),
                                    'page': current_page
                                })
                
                # Increment page number setelah setiap section baru
                if item.get('type') == 'heading' and item.get('content', {}).get('level') == 1:
                    current_page += 1
            
            # Prepare the texts_full format expected by DatabaseManager
            texts_full = [{
                'page': item['page'],
                'text': item['text']
            } for item in processed_text]

            # Buat salinan teks untuk referensi dengan modifikasi minimal
            reference_texts = []
            for item in processed_text:
                # Tambahkan spasi di awal untuk membuat hash berbeda
                modified_text = " " + item['text']
                reference_texts.append({
                    'page': item['page'],
                    'text': modified_text
                })
            
            # Simpan referensi terlebih dahulu
            is_new_ref, ref_doc_id = self.db_manager.check_and_save_document_hash(reference_texts)
            if is_new_ref:
                self.db_manager.store_document(
                    ref_doc_id,
                    reference_texts,
                    results.get('tables', []),
                    results.get('images', []),
                    results.get('metadata', {})
                )
            
            return {
                'texts_full': texts_full,
                'images': results.get('images', []),
                'tables': results.get('tables', []),
                'metadata': results.get('metadata', {}),
                'reference_id': ref_doc_id
            }
            
        except Exception as e:
            logging.error(f"Error processing document: {str(e)}")
            raise
    
    def check_plagiarism(self, file_path, similarity_threshold=0.8):
        """
        Memeriksa plagiarisme dalam dokumen
        """
        try:
            doc_content = self.process_document(file_path)
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            
            # Simpan dokumen dan dapatkan doc_id
            is_new, doc_id = self.db_manager.check_and_save_document_hash(doc_content['texts_full'])
            if is_new:
                self.db_manager.store_document(
                    doc_id,
                    doc_content['texts_full'],
                    doc_content['tables'],
                    doc_content['images'],
                    doc_content['metadata']
                )
            
            report_path = os.path.join(app.config['REPORT_FOLDER'], f'plagiarism_report_{timestamp}.html')
            total_sentences, plagiarized_sentences, similarity_percentage = (
                self.db_manager.calculate_plagiarism(doc_id, report_path)
            )
            
            # Tambahkan metadata ke laporan
            metadata = doc_content['metadata']
            if metadata and os.path.exists(report_path):
                with open(report_path, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                metadata_html = f"""
                <h2>Document Metadata</h2>
                <ul>
                    <li><b>Title:</b> {metadata.get('title', 'N/A')}</li>
                    <li><b>Author:</b> {metadata.get('author', 'N/A')}</li>
                    <li><b>Created:</b> {metadata.get('created', 'N/A')}</li>
                    <li><b>Modified:</b> {metadata.get('modified', 'N/A')}</li>
                    <li><b>Word Count:</b> {metadata.get('word_count', 'N/A')}</li>
                    <li><b>Tables Count:</b> {metadata.get('tables_count', 'N/A')}</li>
                </ul>
                <hr>
                """
                
                content = content.replace('<h2>Document Analysis</h2>', 
                                       metadata_html + '<h2>Document Analysis</h2>')
                
                with open(report_path, 'w', encoding='utf-8') as f:
                    f.write(content)
            
            return {
                'doc_id': doc_id,
                'total_sentences': total_sentences,
                'plagiarized_sentences': plagiarized_sentences,
                'similarity_percentage': similarity_percentage,
                'report_path': report_path,
                'timestamp': timestamp
            }
            
        except Exception as e:
            logging.error(f"Error checking plagiarism: {str(e)}")
            raise

@app.route('/api/check-plagiarism', methods=['POST'])
def check_plagiarism():
    """
    API endpoint untuk mengecek plagiarisme
    """
    try:
        if 'file' not in request.files:
            return jsonify({
                'success': False,
                'message': 'Tidak ada file yang dikirim'
            }), 400
            
        file = request.files['file']
        if file.filename == '':
            return jsonify({
                'success': False,
                'message': 'Tidak ada file yang dipilih'
            }), 400
            
        if not file.filename.lower().endswith('.docx'):
            return jsonify({
                'success': False,
                'message': 'File harus berformat DOCX'
            }), 400
            
        # Simpan file
        filename = secure_filename(file.filename)
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        saved_filename = f"{timestamp}_{filename}"
        file_path = os.path.join(app.config['UPLOAD_FOLDER'], saved_filename)
        file.save(file_path)
        
        logging.info(f"File saved to: {file_path}")
        
        # Inisialisasi checker dengan database manager yang sama untuk semua operasi
        db_manager = DatabaseManager()
        checker = PlagiarismChecker(db_manager)
        
        # Proses file yang diupload
        logging.info("Processing uploaded document...")
        doc_content = checker.process_document(file_path)
        
        if not doc_content['texts_full']:
            raise Exception("No text content found in document")
        
        # Simpan dokumen yang akan dicek
        is_new, doc_id = checker.db_manager.check_and_save_document_hash(doc_content['texts_full'])
        if is_new:
            checker.db_manager.store_document(
                doc_id,
                doc_content['texts_full'],
                doc_content['tables'],
                doc_content['images'],
                doc_content['metadata']
            )
        
        # Lakukan pengecekan plagiarisme dengan dokumen referensi
        report_path = os.path.join(app.config['REPORT_FOLDER'], f'plagiarism_report_{timestamp}.html')
        total_sentences, plagiarized_sentences, similarity_percentage = (
            checker.db_manager.calculate_plagiarism(doc_id, report_path)
        )
        
        logging.info(f"Plagiarism check results - Total: {total_sentences}, Plagiarized: {plagiarized_sentences}, Percentage: {similarity_percentage}%")
        
        # Tambahkan metadata dan gambar ke laporan
        if os.path.exists(report_path):
            with open(report_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Tambahkan metadata
            metadata = doc_content['metadata']
            metadata_html = f"""
            <div class="section">
                <h2>Document Metadata</h2>
                <ul>
                    <li><b>Title:</b> {metadata.get('title', 'N/A')}</li>
                    <li><b>Author:</b> {metadata.get('author', 'N/A')}</li>
                    <li><b>Created:</b> {metadata.get('created', 'N/A')}</li>
                    <li><b>Modified:</b> {metadata.get('modified', 'N/A')}</li>
                    <li><b>Word Count:</b> {metadata.get('word_count', 'N/A')}</li>
                </ul>
            </div>
            <hr>
            """
            
            # Tambahkan gambar dengan path yang benar
            images = doc_content['images']
            if images:
                images_html = """
                <div class="section">
                    <h2>Document Images</h2>
                """
                for image in images:
                    image_path = image.get('path', '')
                    if image_path:
                        # Get just the filename from the full path
                        image_filename = os.path.basename(image_path)
                        image_url = f"http://localhost:5000/images/{image_filename}"
                        caption = image.get('caption', '')
                        logging.info(f"Generated image URL: {image_url}")
                        images_html += f"""
                        <div class="image-container">
                            <img src="{image_url}" alt="{caption}" class="document-image" style="max-width: 100%; height: auto;">
                            <div class="image-caption">{caption}</div>
                        </div>
                        """
                images_html += """
                </div>
                <style>
                    .image-container {
                        margin: 20px 0;
                        text-align: center;
                    }
                    .document-image {
                        max-width: 80%;
                        height: auto;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        margin: 10px auto;
                        display: block;
                    }
                    .image-caption {
                        margin-top: 10px;
                        color: #666;
                        font-style: italic;
                        text-align: center;
                    }
                </style>
                <hr>
                """
                
                # Tambahkan metadata dan gambar sebelum analisis dokumen
                content = content.replace('<h2>Document Analysis</h2>', 
                                       metadata_html + images_html + '<h2>Document Analysis</h2>')
                
                with open(report_path, 'w', encoding='utf-8') as f:
                    f.write(content)
        
        # Hapus file setelah diproses
        os.remove(file_path)
        logging.info("Temporary files cleaned up")
        
        return jsonify({
            'success': True,
            'data': {
                'doc_id': doc_id,
                'total_sentences': total_sentences,
                'plagiarized_sentences': plagiarized_sentences,
                'similarity_percentage': similarity_percentage,
                'report_url': f"/reports/plagiarism_report_{timestamp}.html",
                'timestamp': timestamp
            }
        })
        
    except Exception as e:
        logging.error(f"API Error: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Terjadi kesalahan: {str(e)}'
        }), 500

@app.route('/api/reports/<filename>')
def get_report(filename):
    """
    API endpoint untuk mengambil file laporan
    """
    try:
        report_path = os.path.join(app.config['REPORT_FOLDER'], filename)
        if not os.path.exists(report_path):
            return jsonify({
                'success': False,
                'message': 'Laporan tidak ditemukan'
            }), 404
            
        with open(report_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        return content, 200, {'Content-Type': 'text/html'}
        
    except Exception as e:
        logging.error(f"Error reading report: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Terjadi kesalahan: {str(e)}'
        }), 500

@app.route('/images/<path:filename>')
def serve_image(filename):
    """
    Serve image files from extract_data directory
    """
    try:
        # Log detailed information for debugging
        logging.info(f"Image request received for: {filename}")
        
        # Check in the images directory
        image_path = os.path.join(app.config['IMAGE_FOLDER'], filename)
        logging.info(f"Looking for image at: {image_path}")
        
        if not os.path.exists(image_path):
            # If not found in images directory, check in extract_data
            image_path = os.path.join(app.config['EXTRACT_DATA_FOLDER'], filename)
            logging.info(f"Alternative path check: {image_path}")
            
            if not os.path.exists(image_path):
                logging.error(f"Image file not found in any location: {filename}")
                return jsonify({
                    'success': False,
                    'message': 'Image file not found'
                }), 404
        
        # Get the directory containing the image
        directory = os.path.dirname(image_path)
        filename = os.path.basename(image_path)
        
        logging.info(f"Serving image from directory: {directory}")
        logging.info(f"Image filename: {filename}")
        
        response = send_from_directory(directory, filename)
        response.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate'
        response.headers['Pragma'] = 'no-cache'
        response.headers['Expires'] = '0'
        return response
        
    except Exception as e:
        logging.error(f"Error serving image {filename}: {str(e)}")
        return jsonify({
            'success': False,
            'message': f'Error serving image: {str(e)}'
        }), 500

if __name__ == "__main__":
    # Set up logging configuration
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(levelname)s - %(message)s',
        handlers=[
            logging.FileHandler('plagiarism_check.log'),
            logging.StreamHandler(sys.stdout)
        ]
    )
    
    # Log application startup information
    logging.info("Starting Flask application...")
    logging.info(f"Upload folder: {app.config['UPLOAD_FOLDER']}")
    logging.info(f"Report folder: {app.config['REPORT_FOLDER']}")
    logging.info(f"Extract data folder: {app.config['EXTRACT_DATA_FOLDER']}")
    
    # Run Flask server
    app.run(host='0.0.0.0', port=5000, debug=True)
