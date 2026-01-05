# Sistem Informasi Magang - Krakatau Information Technology (KIT)

Project ini dikembangkan menggunakan **CodeIgniter 3** dengan integrasi **WhatsApp Blast (Node.js)** dan **Brevo Email Service**.

## 📋 Prasyarat Sistem

- **PHP**: 7.3
- **Web Server**: Apache (Rekomendasi: Laragon)
- **Database**: MySQL / MariaDB
- **Runtime**: Node.js (Untuk WhatsApp Server)

---

## Langkah Instalasi (Local Development)

### 1. Persiapan Database

1. Buka **phpMyAdmin** atau tool database lainnya.
2. Buat database baru dengan nama `db_magang_kit`.
3. Import file database yang terletak di: `database/db_magang_kit.sql`.

### 2. Konfigurasi Aplikasi (PHP)

1. Buka file `application/config/database.php` dan sesuaikan username/password database.
2. Rename file `application/config/email.php.example` menjadi `email.php` (jika belum ada) dan lengkapi kredensial SMTP/Brevo di https://login.brevo.com/.
3. Pastikan `$config['base_url']` di `application/config/config.php` sudah mengarah ke folder project Anda (contoh: `http://localhost/magang-kit/`).

### 3. Konfigurasi WhatsApp Server (Node.js)

1. Buka folder `wa-server/`.
2. Rename file `.env.example` menjadi `.env`.
3. Pastikan isi `.env` sebagai berikut:
   ```env
   PORT=3000
   API_KEY=KIT_SECRET_KEY_123456
   session_name=auth_info_baileys
   ```
4. Buka terminal di folder wa-server/ tersebut.
5. Jalankan perintah untuk menginstall dependency:
   ```Bash
   npm install
   ```
6. Jalankan server WhatsApp:
   ```Bash
   npm start
   ```
7. Scan QR Code yang muncul di terminal menggunakan WhatsApp Pribadi/Perusahaan.

Kredensial Default Admin:
Email: admin@kit.go.id
Password: admin123

### NOTE: Untuk sekarang, logika sertifikasi penyelesaian magang menggunakan template sertifikat dari google, ganti asset nya di assets\templates\sertifikat.jpg dan sesuaikan code nya di dalam application\views\laporan\pdf_sertifikat.php
