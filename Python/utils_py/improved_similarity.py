"""
Improved similarity functions for better plagiarism detection
"""

import re
from .needleman_wunsch import needleman_wunsch_similarity

def word_level_similarity(text1, text2):
    """
    Calculate similarity at word level using Jaccard similarity
    """
    # Normalize texts
    text1 = text1.lower().strip()
    text2 = text2.lower().strip()
    
    # Extract words (remove punctuation)
    words1 = set(re.findall(r'\b\w+\b', text1))
    words2 = set(re.findall(r'\b\w+\b', text2))
    
    # Calculate Jaccard similarity
    intersection = len(words1.intersection(words2))
    union = len(words1.union(words2))
    
    return intersection / union if union > 0 else 0

def hybrid_similarity(text1, text2, word_weight=0.6, char_weight=0.4, word_sim=None, char_sim=None):
    """
    Hybrid approach combining word-level and character-level similarity
    """
    if word_sim is None:
        word_sim = word_level_similarity(text1, text2)
    if char_sim is None:
        char_sim = needleman_wunsch_similarity(text1, text2)
    
    return (word_sim * word_weight) + (char_sim * char_weight)

def sequence_similarity(text1, text2):
    """
    Calculate similarity considering word sequence using LCS
    """
    # Normalize and split into words
    words1 = re.findall(r'\b\w+\b', text1.lower())
    words2 = re.findall(r'\b\w+\b', text2.lower())
    
    # Longest Common Subsequence for words
    m, n = len(words1), len(words2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if words1[i-1] == words2[j-1]:
                dp[i][j] = dp[i-1][j-1] + 1
            else:
                dp[i][j] = max(dp[i-1][j], dp[i][j-1])
    
    lcs_length = dp[m][n]
    max_length = max(m, n)
    
    return lcs_length / max_length if max_length > 0 else 0

def enhanced_plagiarism_detection(text1, text2, threshold=0.75):
    """
    Enhanced plagiarism detection using multiple similarity measures
    """
    # 1. FAST PRELIMINARY CHECK (Jaccard similarity)
    word_sim = word_level_similarity(text1, text2)
    
    # 2. EARLY STOPPING
    # Jika tingkat kecocokan kata sangat rendah (<25%), sangat tidak mungkin 
    # terdeteksi sebagai plagiarisme sequence tingkat lanjut
    if word_sim < 0.25:
        return {
            'is_plagiarized': False,
            'max_similarity': word_sim,
            'scores': {
                'word_similarity': word_sim,
                'character_similarity': 0,
                'sequence_similarity': 0,
                'hybrid_similarity': 0
            }
        }
        
    # 3. KALKULASI BERAT (hanya jika lolos tahap 1)
    char_sim = needleman_wunsch_similarity(text1, text2)
    seq_sim = sequence_similarity(text1, text2)
    
    # Pass hasil fungsi yg sudah didapat agar tidak dihitung dua kali
    hybrid_sim = hybrid_similarity(text1, text2, word_sim=word_sim, char_sim=char_sim)
    
    # Gunakan skor tertinggi dari semua metode
    max_similarity = max(word_sim, char_sim, seq_sim, hybrid_sim)
    
    return {
        'is_plagiarized': max_similarity > threshold,
        'max_similarity': max_similarity,
        'scores': {
            'word_similarity': word_sim,
            'character_similarity': char_sim,
            'sequence_similarity': seq_sim,
            'hybrid_similarity': hybrid_sim
        }
    }
