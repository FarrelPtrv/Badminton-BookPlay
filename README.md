# Badminton BookPlay - Sistem Informasi & Booking Lapangan Online

Aplikasi web modern berbasis **Laravel 11**, **MySQL 8.4**, dan **Bootstrap 5** untuk pemesanan lapangan bulutangkis secara real-time, anti *double-booking*, verifikasi pembayaran transfer/QRIS, serta dashboard laporan pendapatan bagi pengelola GOR. Sesuai spesifikasi kebutuhan pada **PRD.md**.

---

## 🌟 Fitur Utama

### 1. Modul Publik & Pelanggan (Customer / Guest)
- **Cek Jadwal Real-Time (Tanpa Login):** Matrix ketersediaan lapangan jam 08:00 - 23:00 WIB dengan indikator warna interaktif:
  - 🟢 **Hijau:** Slot Tersedia (bisa langsung diklik untuk memesan)
  - 🟡 **Kuning:** Menunggu Pembayaran / Verifikasi Pengelola
  - 🔴 **Merah:** Sudah Dibooking (Booked)
  - ⚪ **Abu-abu:** Sedang Dalam Masa Perawatan (Maintenance)
- **Pemesanan Lapangan Multi-Jam:** Form booking responsif dengan perhitungan total harga otomatis secara instan.
- **Sistem Anti Double-Booking (Server-Side Overlap Locking):** Mencegah bentrok jadwal hingga tingkat milidetik menggunakan transaksi database dan row-locking.
- **Batas Waktu Pembayaran (Timeout 30 Menit):** Slot yang belum dibayar dalam 30 menit otomatis dibatalkan dan tersedia kembali.
- **Pembayaran Transfer Bank & QRIS:** Menampilkan nomor rekening resmi (BCA, Mandiri) dan QRIS statis.
- **Upload Bukti Bayar:** Upload foto bukti transfer langsung dari halaman detail pemesanan.
- **E-Ticket & Invoice Resmi:** Bukti booking siap cetak atau simpan PDF lengkap dengan QR Code scanner untuk ditunjukkan ke resepsionis GOR.
- **Profil Pelanggan:** Pengaturan data profil dan ganti password.

### 2. Modul Pengelola (Admin Dashboard)
- **Ringkasan Statistik Keuangan:** Pendapatan hari ini, pendapatan bulan ini, jumlah booking aktif, dan total pelanggan terdaftar.
- **Kelola Lapangan (CRUD):** Tambah, ubah, hapus lapangan, atur tarif sewa per jam, jenis lantai (Karpet Vinyl, Parquet, Semen), dan status operasional.
- **Verifikasi Pembayaran:** Review foto bukti bayar, konfirmasi Lunas, atau tolak pesanan dengan memberikan alasan transparan.
- **Booking Walk-in (Manual Langsung di Tempat):** Input reservasi cepat untuk pelanggan yang datang langsung tanpa akun online.
- **Blokir Jadwal (Maintenance / Turnamen):** Pengelola dapat menutup jadwal tertentu untuk keperluan turnamen atau perbaikan karpet.
- **Laporan Keuangan & Transaksi:** Filter berdasarkan rentang tanggal dan status, fitur **Cetak Laporan PDF** resmi dengan kolom tanda tangan, serta **Ekspor Data ke format Excel/CSV**.

---

## 🔑 Akun Demo Siap Pakai

| Peran (Role) | Alamat Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin (Pengelola)** | `admin@gorbadminton.com` | `password` | Akses penuh dashboard pengelola, kelola lapangan, verifikasi pembayaran, laporan |
| **Customer (Pelanggan)** | `customer@gmail.com` | `password` | Pemesanan lapangan, upload bukti transfer, cetak e-ticket |
| **Customer 2** | `ahmad@gmail.com` | `password` | Akun pelanggan alternatif |

---

## 🚀 Cara Menjalankan Aplikasi

### Cara 1: Menggunakan Script Otomatis (Satu Klik)
Cukup jalankan file:
```cmd
start_app.bat
```
Script ini akan memastikan database MySQL Laragon berjalan, membuka browser ke `http://127.0.0.1:8000`, dan menjalankan server Laravel.

### Cara 2: Menjalankan Secara Manual
1. Pastikan MySQL di Laragon aktif.
2. Buka terminal di folder proyek dan jalankan:
   ```bash
   php artisan serve
   ```
3. Buka browser di [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧪 Pengujian Otomatis (Automated Testing)
Seluruh skenario pengujian utama (termasuk validasi anti double-booking) telah teruji menggunakan PHPUnit:
```bash
php artisan test
```

Hasil Pengujian:
- ✅ `guest can access landing page and schedule` (Lolos)
- ✅ `anti double booking prevents overlapping reservation` (Lolos)
- ✅ `customer can upload payment proof` (Lolos)
- ✅ `admin can verify and confirm booking` (Lolos)
- ✅ `customer cannot access admin dashboard` (Lolos)
- ✅ `admin can access dashboard and create walkin` (Lolos)
