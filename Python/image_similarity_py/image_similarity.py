import torch
import torch.nn as nn
from torchvision import transforms
from PIL import Image
from collections import OrderedDict
import logging
import timm

class EmbeddingNetwork(nn.Module):
    """
    Menggunakan arsitektur EfficientNet-B0.
    """
    def __init__(self, embedding_dim=256):
        super(EmbeddingNetwork, self).__init__()
        # --- PERUBAHAN 1: Buat model TANPA mengunduh bobot pretrained ---
        self.backbone = timm.create_model('efficientnet_b0', pretrained=False)

        feature_dim = self.backbone.get_classifier().in_features
        self.backbone.classifier = nn.Linear(feature_dim, embedding_dim)

    def forward(self, x):
        return self.backbone(x)

class ImageSimilarityModel:
    """
    Kelas untuk mengelola pemuatan model dan inferensi embedding.
    """
    # --- PERUBAHAN 2: Arahkan ke file model lengkap Anda ---
    def __init__(self, model_path='models_AI_py/Siamese-Neural-Network-With-Triplet-Loss/model_final.pth'):
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.embedding_model = EmbeddingNetwork().to(self.device)
        self.model_path = model_path
        self._load_model()

    def _load_model(self):
        """Memuat state_dict model dari file checkpoint."""
        try:
            # --- PERUBAHAN 3: Logika pemuatan menjadi lebih sederhana ---
            # Langsung muat state_dict karena kita menyimpannya secara langsung
            state_dict = torch.load(self.model_path, map_location=self.device)
            self.embedding_model.load_state_dict(state_dict)
            self.embedding_model.eval()
            logging.info(f"Model lokal '{self.model_path}' berhasil dimuat dan berjalan di {self.device}!")

        except FileNotFoundError:
            logging.error(f"Error: File model '{self.model_path}' tidak ditemukan.")
            raise
        except Exception as e:
            logging.error(f"Error saat memuat model: {e}")
            raise

    def _preprocess_image(self, img_path):
        """Menyesuaikan ukuran gambar menjadi 224x224 sesuai model baru."""
        transform = transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
        ])
        try:
            img = Image.open(img_path).convert('RGB')
            return transform(img)
        except FileNotFoundError:
            logging.error(f"File gambar tidak ditemukan di: {img_path}")
            return None

    def get_embedding(self, img_path):
        """Menghasilkan vector embedding dari path file gambar."""
        img_tensor = self._preprocess_image(img_path)
        if img_tensor is None:
            return None

        img_tensor = img_tensor.unsqueeze(0).to(self.device)
        with torch.no_grad():
            embedding = self.embedding_model(img_tensor)
        return embedding.squeeze().cpu().numpy()
