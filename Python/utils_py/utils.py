import re
import logging

logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def is_common_caption(text):
    patterns = [
        r"^(Gambar|Tabel)\s+\d+(\.\d+)*(\.\s*.+)?$",
        r"^(Fig\.|Figure|Table)\s+\d+(\.\d+)*(\.\s*.+)?$"
    ]
    return any(bool(re.match(pattern, text.strip(), re.IGNORECASE)) for pattern in patterns)

def is_heading(text):
    patterns = [
        r"^BAB\s+\d+", 
        r"^\d+\.\d+(\.\d+)*\s+[A-Z]", 
        r"^[A-Z][A-Z\s]+$", 
    ]
    return any(bool(re.match(pattern, text.strip())) for pattern in patterns)

def clean_text(text):
    text = re.sub(r"^\s*[\•\-\)\*]\s+", "", text, flags=re.MULTILINE)
    text = re.sub(r"^\s*(?:\d+\.)+\s+", "", text, flags=re.MULTILINE)
    text = re.sub(r"^\s*[a-z]\.\s+", "", text, flags=re.MULTILINE)
    text = re.sub(r"\s+", " ", text)
    text = text.strip()
    return text

def split_and_fix_sentences(text):
    try:
        lines = text.split('\n')
        sentences = []
        current_sentence = []
        
        for line in lines:
            line = line.strip()
            if not line: continue
            if is_heading(line) or is_common_caption(line):
                if current_sentence:
                    sentences.append(' '.join(current_sentence))
                    current_sentence = []
                sentences.append(line)
                continue
            
            if len(line) <= 3 or re.match(r"^\d+(\.\d+)*$", line):
                current_sentence.append(line)
                continue
            
            if line[0].islower() and current_sentence:
                current_sentence.append(line)
            else:
                if current_sentence:
                    sentences.append(' '.join(current_sentence))
                current_sentence = [line]
        
        if current_sentence:
            sentences.append(' '.join(current_sentence))
        
        sentences = [clean_text(s) for s in sentences if s.strip()]
        
        return sentences
        
    except Exception as e:
        logging.error(f"Error in split_and_fix_sentences: {str(e)}")
        return [text]

def preprocess_text(text):
    try:
        text = text.lower()
        text = ''.join(char for char in text if char.isalnum() or char.isspace())
        text = ' '.join(text.split())
        return text
    except Exception as e:
        logging.error(f"Error in preprocess_text: {str(e)}")
        return text
