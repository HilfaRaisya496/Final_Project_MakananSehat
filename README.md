# Web Aplikasi Rekomendasi Makanan Sehat

Aplikasi web untuk membantu mahasiswa merencanakan menu sehat, mencatat makanan harian, dan memantau asupan kalori serta makronutrien. Aplikasi ini menggunakan Edamam API untuk rekomendasi resep dan analisis gizi.

## Fitur Utama

- Registrasi, login, dan logout (password di-hash dengan `password_hash` / `password_verify`).
- Profil gizi per user: target kalori harian, jenis diet, dan intolerances (bahan yang dihindari).
- Rekomendasi meal plan 3x sehari (sarapan, makan siang, makan malam) berdasarkan profil user dan Edamam API.
- Pencarian makanan/resep dan penambahan ke food log.
- Food log harian dengan CRUD (tambah dari rekomendasi/search, lihat, dan hapus).
- Dashboard gizi:
  - Grafik kalori per hari (7 hari terakhir).
  - Grafik komposisi makro (protein, karbo, lemak).
- AnalyticsService:
  - Rata-rata kalori vs target.
  - Hari over/under target.
  - Analisis rasio makro dan rekomendasi pola makan.
- Notifikasi:
  - Email pengingat menu sehat harian (via PHPMailer + cron).
  - Log semua notifikasi ke tabel `notifications`.
- Export CSV laporan food log (kalori & makro per makanan).

## Teknologi

- PHP native (tanpa framework MVC).
- MySQL/MariaDB.
- Composer (PHPMailer).
- Frontend: Bootstrap 5, Chart.js untuk grafik. 
- Edamam Recipe API untuk data resep dan nutrisi. 

## Struktur Folder Singkat

- `/app`
  - `/core` – `bootstrap.php`, koneksi DB, helper, autentikasi.
  - `/models` – `User.php`, `UserProfile.php`, `FoodRecommendation.php`, `Notification.php`, dll.
  - `/services` – `RecommendationService.php`, `AnalyticsService.php`, `NotificationService.php`.
  - `/api` – `EdamamClient.php`.
- `/dashboard`
  - `/user` – halaman user: `user.php`, `rekomendasi.php`, `cari_makanan.php`, `food_logs_index.php`, `edit_profile.php`, `dashboard_gizi.php`, dll.
  - `/admin` – halaman admin: `users.php`, `notifications.php`, dll.
- `cron_send_menu_reminder.php` – script cron pengingat menu harian.
- `.env` – konfigurasi (DB, Edamam API, SMTP).
- `index.php` – tampilan awal(promosi).

## Cara Menjalankan di Lokal

Panduan singkat untuk menjalankan proyek ini secara lokal.

1. Clone repo dan masuk ke folder proyek:

  ```bash
  git clone <repo-url>
  cd Final_ProjectMakananSehat
  ```

2. Instal dependensi PHP via Composer:

  ```bash
  composer install
  ```

3. Buat file `.env` di root proyek jika belum ada. Contoh variabel yang harus diisi:

  ```env
  DB_HOST=127.0.0.1
  DB_NAME=your_database
  DB_USER=your_user
  DB_PASS=your_password

  EDAMAM_APP_ID=your_edamam_app_id
  EDAMAM_APP_KEY=your_edamam_app_key

  SMTP_HOST=smtp.example.com
  SMTP_PORT=587
  SMTP_USER=smtp_user
  SMTP_PASS=smtp_pass
  SMTP_FROM_EMAIL=from@example.com
  SMTP_FROM_NAME="TriHealth"
  ```

  Catatan: proyek menggunakan `vlucas/phpdotenv` melalui `config/env.php` untuk memuat file `.env` dari folder root.

4. Buat database MySQL/MariaDB dan import skema jika tersedia. Repo ini tidak menyertakan file SQL contoh; jika Anda punya file SQL (mis. `database/schema.sql`), import dengan:

  ```bash
  mysql -u DB_USER -p DB_NAME < path/to/schema.sql
  ```

5. Jalankan aplikasi :

  - Dengan XAMPP/Apache: set `DocumentRoot` ke folder `index.php` (contoh: `C:/xampp/htdocs/final_project_makanansehat`) lalu akses `http://localhost/final_project_makanansehat/` atau rute yang sesuai.

6. (Opsional) Jika Anda ingin menjalankan cron pengingat harian secara manual:

  ```bash
  php cron_send_menu_reminder.php
  ```

7. Jika perlu, buat akun admin langsung di database.

## Akun Demo

- Admin  
- Email: `admin123@gmail.com`  
- Password: `admin123`

- User biasa  
- Email: `hilfaraisya01@gmail.com`  
- Password: `123456`

## Cron Job (Notifikasi Harian)

Di server produksi, jalankan script pengingat harian dengan cron, contoh:

Script ini akan menghasilkan meal plan untuk setiap user aktif dan mengirim email pengingat menu sehat. Log pengiriman disimpan di tabel `notifications`.



