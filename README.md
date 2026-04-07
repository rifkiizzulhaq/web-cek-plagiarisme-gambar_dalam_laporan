# Web Cek Plagiarisme Gambar Dalam Laporan

Project ini adalah aplikasi web berbasis Laravel dan Python untuk membantu pengecekan plagiarisme pada laporan berformat `.docx`.

Fitur utamanya mencakup:
- autentikasi mahasiswa dan admin
- login Google untuk pengguna tertentu
- upload dokumen `.docx`
- deteksi kemiripan teks
- deteksi kemiripan gambar
- preview dokumen hasil konversi
- highlighting kalimat terindikasi dan sitasi sumber
- CRUD data mahasiswa oleh admin
- export data mahasiswa ke Excel

## Stack

- Backend web: Laravel 10
- Frontend build tool: Vite + Tailwind CSS + Alpine.js
- Python service: Flask
- Database: MySQL
- Konversi dokumen: `python-docx`, `phpoffice/phpword`, `mammoth`
- Image similarity: `torch`, `torchvision`, `timm`

## Struktur Singkat

- `app/` berisi controller, model, middleware, rules, dan command Laravel
- `resources/views/` berisi blade untuk admin, mahasiswa, auth, dan preview dokumen
- `database/migrations/` berisi skema database Laravel dan tabel pendukung analisis
- `database/seeders/` berisi seeder role dan akun awal
- `Python/` berisi service Flask untuk ekstraksi dokumen dan analisis plagiarisme

## Prasyarat

Sebelum setup, pastikan environment memiliki:

- PHP 8.1+
- Composer
- Node.js 18+ dan npm
- Python 3.9+ disarankan
- MySQL / MariaDB

## Environment Yang Perlu Diisi

Setelah copy `.env.example` menjadi `.env`, minimal isi bagian berikut:

```env
APP_NAME="Web Cek Plagiarisme"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=plagiarism_db
DB_USERNAME=root
DB_PASSWORD=

PYTHON_API_URL=http://127.0.0.1:5000

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Catatan:
- `DB_DATABASE` sebaiknya sama untuk Laravel dan service Python.
- `PYTHON_API_URL` harus mengarah ke service Flask yang berjalan.
- konfigurasi Google opsional, tetapi wajib jika fitur login Google ingin dipakai.

## Cara Setup

### 1. Clone repository

```bash
git clone https://github.com/rifkiizzulhaq/web-cek-plagiarisme-gambar_dalam_laporan.git
cd web-cek-plagiarisme-gambar_dalam_laporan
```

### 2. Setup Laravel

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Jika `copy` tidak tersedia, buat `.env` secara manual dari `.env.example`.

### 3. Setup frontend

```bash
npm install
npm run build
```

Saat development, gunakan:

```bash
npm run dev
```

### 4. Setup service Python

Masuk ke folder Python:

```bash
cd Python
python -m venv .venv
```

Aktifkan virtual environment:

Windows PowerShell:

```powershell
.venv\Scripts\Activate.ps1
```

Lalu install dependency:

```bash
pip install -r requirements.txt
```

Jalankan service Flask:

```bash
python main.py
```

Service Python default berjalan di `http://127.0.0.1:5000`.

### 5. Jalankan aplikasi Laravel

Di root project:

```bash
php artisan serve
```

Jika mode development frontend dipakai, jalankan bersamaan:

```bash
npm run dev
```

## Urutan Menjalankan Project Saat Development

Biasanya terminal yang dibutuhkan ada 3:

1. `php artisan serve`
2. `npm run dev`
3. `cd Python` lalu `python main.py`

## Seeder Default

Seeder akan membuat role dan akun awal.

Akun admin default:

- Email: `admin@gmail.com`
- Password: `12345678`

Jika ingin menambah atau mengubah akun awal, edit file:
- `database/seeders/UserSeeder.php`
- `database/seeders/RoleSeeder.php`

## Fitur Utama Aplikasi

### Mahasiswa

- melengkapi profil
- upload laporan `.docx`
- melihat hasil kemiripan teks dan gambar
- melihat riwayat upload
- membuka preview dokumen dengan highlight dan sitasi sumber

### Admin

- login admin
- CRUD mahasiswa
- export data mahasiswa
- melihat dokumen yang dimiliki mahasiswa dari halaman detail

## Command Tambahan

Untuk membersihkan dokumen upload dan data plagiarisme tanpa menghapus akun user:

```bash
php artisan docs:clear
```

Command ini akan membersihkan:
- tabel hasil analisis
- file upload dokumen
- hasil ekstraksi gambar Python

Gunakan dengan hati-hati.

## Catatan Teknis

- Dokumen yang sama dicek dengan hash untuk mencegah upload duplikat.
- Service Python membaca dokumen, memecah teks, menyimpan metadata, lalu menghitung kemiripan teks dan gambar.
- Preview dokumen di browser memanfaatkan `mammoth` untuk menampilkan konten `.docx`.
- Beberapa model AI dan file ekstraksi bisa berukuran besar, jadi hindari memasukkan file generated ke commit yang tidak perlu.

## Troubleshooting

### Laravel tidak bisa terhubung ke Python

Periksa:
- `PYTHON_API_URL` di `.env`
- service Flask benar-benar berjalan
- port `5000` tidak dipakai aplikasi lain

### Upload gagal karena database

Periksa:
- database sudah dibuat
- `.env` berisi `DB_*` yang benar
- migrasi sudah dijalankan

### Login Google tidak jalan

Periksa:
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI`
- redirect URI di Google Console harus sama persis

## Lisensi

Project ini menggunakan basis Laravel yang berlisensi MIT. Kode pengembangan project ini mengikuti kebutuhan aplikasi skripsi / akademik sesuai repository ini.
