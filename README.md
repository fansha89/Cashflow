# Cashflow

Cashflow adalah aplikasi manajemen keuangan yang dirancang untuk membantu pengguna dalam mencatat dan mengelola pemasukan serta pengeluaran secara efisien.

## Fitur Utama
- **Manajemen Produk**: Tambah, ubah, hapus, dan lihat produk dengan mudah.
- **Template Produk**: Unduh template untuk memudahkan pengisian data produk.
- **Manajemen Transaksi**: Catat pemasukan dan pengeluaran untuk analisis keuangan.
- **Autentikasi dan Keamanan**: Menggunakan Laravel Breeze untuk autentikasi pengguna.
- **RESTful API**: Menggunakan Laravel sebagai backend untuk komunikasi antar sistem.

## Teknologi yang Digunakan
- **Framework**: Laravel 10
- **Database**: MySQL
- **Autentikasi**: Laravel Breeze
- **Cloud Hosting**: Microsoft Azure

## Instalasi dan Konfigurasi
### 1. Clone Repository
```sh
git clone git@github.com:fansha89/Cashflow.git
cd Cashflow
```

### 2. Instal Dependensi
```sh
composer install
npm install && npm run dev
```

### 3. Konfigurasi Environment
Buat file `.env` berdasarkan `.env.example` lalu atur database dan konfigurasi lainnya.
```sh
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi Database
```sh
php artisan migrate --seed
```

### 5. Menjalankan Aplikasi
```sh
php artisan serve
```
Aplikasi dapat diakses di `http://127.0.0.1:8000`

## API Endpoints
| Method | Endpoint | Deskripsi |
|--------|----------|------------|
| GET | /api/products | Mendapatkan semua produk |
| GET | /api/products/{id} | Mendapatkan produk berdasarkan ID |
| POST | /api/products | Menambahkan produk baru |
| PUT | /api/products/{id} | Memperbarui data produk |
| DELETE | /api/products/{id} | Menghapus produk |

## Kontributor
- **Fansha Fakhriza** (fansha89)

## Lisensi
Proyek ini menggunakan lisensi **MIT**.

