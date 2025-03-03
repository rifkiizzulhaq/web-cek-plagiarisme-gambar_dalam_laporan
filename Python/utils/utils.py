import re
from nltk.tokenize import sent_tokenize
import string
import logging

# Konfigurasi logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def is_common_caption(text):
    """
    Deteksi apakah teks adalah caption umum seperti 'Gambar 1.' atau 'Tabel 2.'.
    Juga mendeteksi format lain seperti 'Fig. 1' atau 'Table 2.1'
    """
    patterns = [
        r"^(Gambar|Tabel)\s+\d+(\.\d+)*(\.\s*.+)?$",
        r"^(Fig\.|Figure|Table)\s+\d+(\.\d+)*(\.\s*.+)?$"
    ]
    return any(bool(re.match(pattern, text.strip(), re.IGNORECASE)) for pattern in patterns)

def is_heading(text):
    """
    Deteksi apakah teks adalah heading berdasarkan pola umum
    """
    patterns = [
        r"^BAB\s+\d+",  # BAB 1, BAB 2, etc.
        r"^\d+\.\d+(\.\d+)*\s+[A-Z]",  # 1.1 PENDAHULUAN, 2.1.1 Metode, etc.
        r"^[A-Z][A-Z\s]+$",  # PENDAHULUAN, METODOLOGI PENELITIAN, etc.
    ]
    return any(bool(re.match(pattern, text.strip())) for pattern in patterns)

def clean_text(text):
    """
    Membersihkan teks dengan menghapus nomor urut dan karakter khusus,
    tetapi mempertahankan struktur dokumen yang penting.
    """
    # Hapus karakter khusus di awal baris
    text = re.sub(r"^\s*[\•\-\)\*]\s+", "", text, flags=re.MULTILINE)
    
    # Hapus nomor urut di awal paragraf (1., 1.1., a., dst)
    text = re.sub(r"^\s*(?:\d+\.)+\s+", "", text, flags=re.MULTILINE)
    text = re.sub(r"^\s*[a-z]\.\s+", "", text, flags=re.MULTILINE)
    
    # Normalisasi spasi
    text = re.sub(r"\s+", " ", text)
    
    # Hapus spasi berlebih
    text = text.strip()
    
    return text

def split_and_fix_sentences(text):
    """
    Memisahkan teks menjadi kalimat dengan mempertahankan struktur dokumen.
    Menggabungkan baris-baris yang terpisah namun masih satu kalimat.
    """
    try:
        # Split berdasarkan baris
        lines = text.split('\n')
        sentences = []
        current_sentence = []
        
        for line in lines:
            line = line.strip()
            if not line:  # Skip baris kosong
                continue
                
            # Jika heading atau caption, langsung tambahkan sebagai kalimat terpisah
            if is_heading(line) or is_common_caption(line):
                if current_sentence:
                    sentences.append(' '.join(current_sentence))
                    current_sentence = []
                sentences.append(line)
                continue
            
            # Jika baris sangat pendek atau hanya berisi nomor
            if len(line) <= 3 or re.match(r"^\d+(\.\d+)*$", line):
                current_sentence.append(line)
                continue
            
            # Jika baris diawali huruf kecil, gabungkan dengan kalimat sebelumnya
            if line[0].islower() and current_sentence:
                current_sentence.append(line)
            else:
                # Jika ada kalimat yang sedang diproses, selesaikan dulu
                if current_sentence:
                    sentences.append(' '.join(current_sentence))
                current_sentence = [line]
        
        # Tambahkan kalimat terakhir jika ada
        if current_sentence:
            sentences.append(' '.join(current_sentence))
        
        # Bersihkan hasil akhir
        sentences = [clean_text(s) for s in sentences if s.strip()]
        
        return sentences
        
    except Exception as e:
        logging.error(f"Error in split_and_fix_sentences: {str(e)}")
        return [text]  # Return original text as single sentence if error occurs

def preprocess_text(text):
    """
    Membersihkan teks untuk perbandingan dengan menghilangkan karakter yang tidak relevan
    dan menstandardisasi format.
    """
    try:
        # Konversi ke lowercase
        text = text.lower()
        
        # Hapus karakter khusus dan tanda baca
        text = ''.join(char for char in text if char.isalnum() or char.isspace())
        
        # Normalisasi spasi
        text = ' '.join(text.split())
        
        return text
    except Exception as e:
        logging.error(f"Error in preprocess_text: {str(e)}")
        return text  # Return original text if error occurs
