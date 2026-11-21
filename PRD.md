# Product Requirements Document (PRD)
**Nama Proyek:** Sistem Informasi & Booking Lapangan Badminton (GOR Badminton)  
**Versi:** 1.0  
**Tanggal:** 14 November 2026  
**Tech Stack:** Laravel 11, MySQL, Bootstrap 5  

---

## 1. Ringkasan Proyek (Project Overview)
### 1.1 Latar Belakang
Pengelolaan jadwal penyewaan lapangan badminton secara manual (melalui buku catatan atau pesan WhatsApp) sering kali menimbulkan risiko *double booking* (jadwal bentrok), kesulitan merekap laporan keuangan, serta kurangnya transparansi ketersediaan jadwal bagi pelanggan. 

### 1.2 Tujuan
Membangun aplikasi berbasis web yang memudahkan pelanggan untuk melihat jadwal ketersediaan lapangan secara *real-time*, melakukan pemesanan (*booking*), dan mengunggah bukti pembayaran, sekaligus mempermudah pengelola (Admin) dalam memvalidasi pesanan dan memantau laporan pendapatan.

---

## 2. Arsitektur & Teknologi (Tech Stack)
* **Backend Framework:** Laravel 11 (PHP 8.2+)
* **Database:** MySQL 8.0+
* **Frontend / UI:** Blade Templating Engine + Bootstrap 5 (Responsif untuk Desktop & Mobile)
* **Authentication:** Laravel Breeze / Laravel UI (Bootstrap Auth)
* **Icon & Library Pendukung:** Bootstrap Icons / FontAwesome, DataTables (untuk tabel admin), SweetAlert2 (untuk notifikasi interaktif).

---

## 3. Peran Pengguna (User Roles)
1. **Guest (Pengunjung):** Dapat melihat informasi GOR, daftar lapangan, harga sewa, dan cek jadwal ketersediaan lapangan tanpa harus login.
2. **Customer (Pelanggan Terdaftar):** Dapat melakukan pemesanan lapangan, mengunggah bukti transfer, melihat riwayat booking, dan mencetak bukti booking (E-Ticket/Invoice).
3. **Admin (Pengelola GOR):** Memiliki akses penuh ke dashboard admin untuk mengelola data lapangan, mengatur jadwal/harga, memverifikasi pembayaran, memblokir jadwal (untuk *maintenance* atau turnamen), dan melihat laporan keuangan.

---

## 4. Fitur Utama (Functional Requirements)

### 4.1 Modul Autentikasi & Profil
* Registrasi akun baru untuk pelanggan (Nama, Email, No. WhatsApp, Password).
* Login dan Logout untuk Customer dan Admin (menggunakan sistem *middleware* role).
* Edit profil dan ganti password.

### 4.2 Modul Manajemen Lapangan (Admin)
* **CRUD Lapangan:** Tambah, lihat, ubah, dan hapus data lapangan (Nama Lapangan, Jenis Lantai seperti Karpet Vinyl/Semen/Kayu, Deskripsi, Foto Lapangan, Status Aktif/Nonaktif).
* **Pengaturan Harga:** Mengatur tarif sewa per jam (opsi tambahan: perbedaan harga *Weekday* vs *Weekend* atau Pagi/Siang vs Malam).

### 4.3 Modul Cek Jadwal & Booking (Customer)
* **Cek Ketersediaan Real-time:** Pelanggan memilih tanggal dan lapangan, sistem menampilkan slot jam operasional (misal: 08:00 - 23:00) dengan indikator warna:
  * *Hijau:* Tersedia
  * *Kuning:* Menunggu Pembayaran / Verifikasi
  * *Merah:* Sudah Dibooking (*Booked*)
* **Form Pemesanan:** Memilih durasi sewa (bisa memilih lebih dari 1 jam berturut-turut) dan sistem otomatis menghitung total harga.
* **Batas Waktu Pembayaran:** Pesanan berstatus `Pending` memiliki batas waktu (misal: 30 menit). Jika melewati batas waktu tanpa upload bukti bayar, slot otomatis dibatalkan/tersedia kembali.

### 4.4 Modul Pembayaran & Verifikasi
* **Metode Pembayaran:** Transfer Bank Manual (menampilkan nomor rekening GOR) atau QRIS statis.
* **Upload Bukti Bayar:** Pelanggan mengunggah foto/screenshot bukti transfer.
* **Verifikasi Admin:** Admin mengecek mutasi dan mengubah status booking menjadi `Confirmed` (Lunas) atau `Rejected` (Ditolak dengan alasan).

### 4.5 Modul Dashboard & Laporan (Admin)
* **Ringkasan Statistik:** Total pendapatan hari ini/bulan ini, jumlah booking aktif, jumlah pelanggan terdaftar.
* **Manual Booking (Walk-in):** Admin dapat menginput pesanan secara langsung untuk pelanggan yang datang ke lokasi tanpa mendaftar online.
* **Laporan Transaksi:** Filter laporan berdasarkan rentang tanggal dan status pembayaran, serta fitur *Export* ke Excel/PDF.

---

## 5. Alur Pengguna (User Flow)

### Alur Booking Pelanggan:
1. Pelanggan membuka halaman utama $\rightarrow$ Klik menu **"Cek Jadwal / Booking"**.
2. Memilih **Tanggal Main** dan **Lapangan**.
3. Memilih **Jam Mulai** dan **Durasi** (misal: 19:00 - 21:00 / 2 Jam).
4. Jika belum login, diarahkan ke halaman **Login/Register**.
5. Masuk ke halaman **Checkout / Ringkasan Pesanan** $\rightarrow$ Klik **"Buat Pesanan"**.
6. Melakukan transfer sesuai nominal dan mengunggah bukti pembayaran di halaman **Riwayat Booking**.
7. Admin memverifikasi pembayaran $\rightarrow$ Status berubah menjadi **Confirmed**.
8. Pelanggan menunjukkan halaman detail invoice/kode booking kepada petugas GOR saat tiba di lokasi.

---

## 6. Perancangan Database (MySQL Schema)

Berikut adalah struktur tabel utama yang dibutuhkan:

### 1. `users`
* `id` (BIGINT, PK, Auto Increment)
* `name` (VARCHAR)
* `email` (VARCHAR, Unique)
* `phone` (VARCHAR)
* `password` (VARCHAR)
* `role` (ENUM: `'admin'`, `'customer'`, Default: `'customer'`)
* `created_at`, `updated_at` (TIMESTAMP)

### 2. `courts` (Data Lapangan)
* `id` (BIGINT, PK, Auto Increment)
* `name` (VARCHAR) — *Contoh: Lapangan 1 (Karpet)*
* `description` (TEXT)
* `price_per_hour` (DECIMAL/INT)
* `image` (VARCHAR, Nullable)
* `status` (ENUM: `'available'`, `'maintenance'`, Default: `'available'`)
* `created_at`, `updated_at` (TIMESTAMP)

### 3. `bookings` (Transaksi Pemesanan)
* `id` (BIGINT, PK, Auto Increment)
* `booking_code` (VARCHAR, Unique) — *Contoh: BDM-20261114-001*
* `user_id` (BIGINT, FK ke `users.id`)
* `court_id` (BIGINT, FK ke `courts.id`)
* `booking_date` (DATE)
* `start_time` (TIME) — *Contoh: 18:00:00*
* `end_time` (TIME) — *Contoh: 20:00:00*
* `duration` (INT) — *Dalam satuan jam*
* `total_price` (INT)
* `status` (ENUM: `'pending'`, `'waiting_verification'`, `'confirmed'`, `'cancelled'`)
* `notes` (TEXT, Nullable)
* `created_at`, `updated_at` (TIMESTAMP)

### 4. `payments` (Data Pembayaran)
* `id` (BIGINT, PK, Auto Increment)
* `booking_id` (BIGINT, FK ke `bookings.id`)
* `payment_method` (VARCHAR) — *Contoh: Transfer BCA, QRIS, Cash (Walk-in)*
* `proof_of_payment` (VARCHAR, Nullable) — *Path file gambar*
* `payment_date` (DATETIME)
* `verified_by` (BIGINT, FK ke `users.id`, Nullable)
* `created_at`, `updated_at` (TIMESTAMP)

---

## 7. Kebutuhan Non-Fungsional
* **Validasi Bentrok Jadwal (Anti Double-Booking):** Sistem wajib melakukan pengecekan irisan waktu (*time overlap*) di sisi server (Laravel Controller) sebelum menyimpan data booking ke database.
* **Desain Responsif:** Menggunakan *grid system* Bootstrap 5 sehingga tampilan tetap rapi dan mudah digunakan melalui browser smartphone maupun desktop.
* **Keamanan:** Proteksi CSRF pada setiap form, validasi tipe file dan ukuran maksimal (maks. 2MB, format JPG/PNG) saat unggah bukti bayar, serta *hashing* password menggunakan Bcrypt.

---

## 8. Tahapan Pengembangan (Milestones)
* **Fase 1 (Setup & Auth):** Instalasi Laravel 11, integrasi Bootstrap 5, konfigurasi database MySQL, dan pembuatan sistem autentikasi multi-role (Admin & Customer).
* **Fase 2 (Master Data):** Pembuatan fitur CRUD Lapangan pada halaman Admin serta tampilan katalog lapangan di halaman depan.
* **Fase 3 (Core Booking Engine):** Pembuatan logika cek ketersediaan jadwal, validasi bentrok waktu, dan proses pembuatan transaksi booking.
* **Fase 4 (Payment & Verification):** Fitur upload bukti pembayaran oleh Customer dan fitur verifikasi oleh Admin.
* **Fase 5 (Reporting & Testing):** Pembuatan dashboard statistik, export laporan, serta uji coba skenario *double booking* dan responsivitas UI.