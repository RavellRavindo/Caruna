# Caruna

Caruna adalah aplikasi web untuk mempertemukan klien yang membutuhkan pendampingan/perawatan dengan caregiver. Aplikasi ini dikembangkan sebagai proyek akademik menggunakan Laravel, MySQL, Tailwind CSS, dan Midtrans Snap.

## Fitur utama

- Registrasi, login, pengaturan profil, dan pengelolaan nomor WhatsApp/alamat.
- Manajemen data pasien milik klien.
- Katalog caregiver terverifikasi dengan spesialisasi, tarif, pengalaman, dan ulasan.
- Booking dengan validasi kepemilikan pasien, ketersediaan caregiver, serta bentrok jadwal.
- Pembayaran Midtrans Snap dengan callback bertanda tangan dan polling status pembayaran.
- Alamat layanan serta kontak pasangan booking baru dibuka setelah pembayaran berhasil.
- Caregiver dapat menerima booking, memulai/menyelesaikan layanan, melihat saldo, mengajukan penarikan, serta mengubah tarif harian.
- Admin dapat memverifikasi caregiver, memproses penarikan, dan mencatat rekonsiliasi refund pembayaran.
- Ulasan satu kali untuk setiap booking yang telah selesai.
- Scheduler untuk membatalkan booking yang melewati batas waktu.

## Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP 8.2+, Laravel 12 |
| Database | MySQL 8+ / MariaDB |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Pembayaran | Midtrans PHP SDK / Snap |
| Testing | PHPUnit |

> Vite pada proyek ini memerlukan Node.js 20.19+ atau 22.12+. Gunakan versi tersebut agar build frontend tidak menampilkan peringatan.

## Peran dan alur aplikasi

| Peran | Kemampuan |
| --- | --- |
| Klien | Mengelola pasien, membuat booking, membayar, mengonfirmasi layanan selesai, dan memberi ulasan. |
| Caregiver | Mendaftar dengan data profesional, menunggu verifikasi admin, mengelola tarif, menangani booking, saldo, dan penarikan. |
| Admin | Menyetujui/menolak caregiver, menangani penarikan saldo, dan mencatat refund pembayaran terlambat. |

Alur booking utama:

~~~text
pending -> approved -> paid -> ongoing -> waiting_confirmation -> completed
~~~

Booking dapat menjadi rejected atau canceled. Booking pending atau approved akan dibatalkan setelah 12 jam bila tidak ditindaklanjuti.

## Prasyarat

- PHP 8.2 atau lebih baru
- Composer 2
- Node.js 20.19+ dan npm
- MySQL 8+ atau MariaDB
- Akun Midtrans Sandbox untuk development pembayaran
- Opsional: ngrok untuk menerima callback Midtrans di localhost

## Instalasi lokal

1. Clone repository dan masuk ke folder proyek.

   ~~~powershell
   git clone <URL_REPOSITORY>
   cd Caruna
   ~~~

2. Instal dependensi backend dan frontend.

   ~~~powershell
   composer install
   npm install
   ~~~

3. Buat file environment dan application key.

   ~~~powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ~~~

4. Atur database dan Midtrans pada file .env.

   ~~~dotenv
   APP_NAME=Caruna
   APP_ENV=local
   APP_DEBUG=false
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=caruna
   DB_USERNAME=root
   DB_PASSWORD=

   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ~~~

   Jangan pernah memasukkan MIDTRANS_SERVER_KEY, APP_KEY, atau password database ke repository.

5. Jalankan migrasi dan data contoh.

   ~~~powershell
   php artisan migrate --seed
   ~~~

6. Bangun aset frontend.

   ~~~powershell
   npm run build
   ~~~

## Menjalankan aplikasi saat development

Jalankan perintah berikut pada terminal terpisah:

~~~powershell
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite hot reload
npm run dev

# Terminal 3: scheduler booking kedaluwarsa
php artisan schedule:work
~~~

Alternatifnya, composer run dev menjalankan server Laravel, queue listener, log, dan Vite sekaligus. Perintah tersebut **tidak** menjalankan scheduler, sehingga php artisan schedule:work tetap harus dijalankan di terminal lain.

Untuk menjalankan pengecekan scheduler satu kali:

~~~powershell
php artisan schedule:run
~~~

## Akun seed untuk development

Jalankan php artisan migrate --seed, lalu gunakan akun berikut hanya pada lingkungan lokal:

| Peran | Email | Password |
| --- | --- | --- |
| Admin | admin@caruna.com | password |
| Klien | klien@caruna.com | password |
| Caregiver | siti@caruna.com | password123 |
| Caregiver | ahmad@caruna.com | password123 |

Ganti atau hapus kredensial contoh tersebut sebelum deployment.

## Konfigurasi Midtrans dan callback lokal

1. Gunakan Server Key dan Client Key **Sandbox** dari dashboard Midtrans pada file .env.
2. Jalankan aplikasi di port 8000.

   ~~~powershell
   php artisan serve
   ~~~

3. Buka tunnel publik dari terminal lain.

   ~~~powershell
   ngrok http 8000
   ~~~

4. Salin URL HTTPS ngrok, misalnya https://contoh.ngrok-free.app.
5. Di dashboard Midtrans Sandbox, buka **Settings → Configuration → Payment Notification URL**, lalu isi:

   ~~~text
   https://contoh.ngrok-free.app/midtrans-callback
   ~~~

6. Bila APP_URL diubah, bersihkan cache konfigurasi:

   ~~~powershell
   php artisan config:clear
   ~~~

Endpoint callback adalah POST /midtrans-callback. Endpoint ini dikecualikan dari CSRF karena diverifikasi menggunakan signature_key Midtrans dan nominal pembayaran harus sama dengan data payment lokal.

### Status pembayaran dan refund

- Callback settlement, atau capture dengan fraud status diterima, mengubah payment menjadi sukses dan booking approved menjadi paid.
- Booking yang sudah canceled tidak akan diaktifkan kembali otomatis.
- Bila pembayaran sukses tiba setelah booking dibatalkan, payment diberi status rekonsiliasi refund_required.
- Klien melihat notifikasi refund pada riwayat booking.
- Admin membuka menu **Refund Pembayaran**, memproses refund di Midtrans Dashboard/kanal pembayaran, lalu memasukkan nomor referensi refund.
- Tombol admin hanya mencatat refund yang telah diproses; tombol tersebut tidak mengirim dana secara otomatis.
- Callback Midtrans dengan status refund menandai rekonsiliasi sebagai refunded.

## Caregiver dan verifikasi

Saat mendaftar sebagai caregiver, pengguna wajib mengisi spesialisasi, tarif per hari, pengalaman, dan jenis kelamin. Sistem membuat profil caregiver dengan status pending.

Sebelum admin menyetujui:

- caregiver tidak muncul di katalog;
- caregiver tidak dapat menerima pesanan atau membuka dompet;
- caregiver dapat melihat status pendaftarannya di dashboard.

Setelah disetujui, caregiver menjadi tersedia untuk booking. Caregiver juga dapat mengubah tarif harian dari **Profil Saya > Tarif layanan**. Perubahan tarif hanya berlaku untuk booking baru karena nilai booking disimpan sebagai snapshot_price.

## Pengujian dan kualitas kode

Jalankan seluruh test:

~~~powershell
php artisan test
~~~

Atau gunakan script Composer:

~~~powershell
composer test
~~~

Periksa gaya kode PHP tanpa mengubah file:

~~~powershell
vendor\bin\pint --test
~~~

Build aset production:

~~~powershell
npm run build
~~~

## Struktur penting

~~~text
app/
├── Console/Commands/CancelExpiredBookings.php
├── Http/Controllers/
│   ├── BookingController.php
│   ├── PaymentCallbackController.php
│   ├── AdminCaregiverController.php
│   └── AdminPaymentReconciliationController.php
├── Http/Middleware/
│   ├── CheckRole.php
│   └── EnsureVerifiedCaregiver.php
└── Models/
    ├── Booking.php
    ├── Caregiver.php
    ├── Payment.php
    └── WalletTransaction.php

database/
├── migrations/
└── seeders/

resources/views/
├── admin/
├── bookings/
├── caregivers/
└── profile/
~~~

## Catatan deployment

Sebelum production:

- Set APP_ENV=production, APP_DEBUG=false, dan MIDTRANS_IS_PRODUCTION=true.
- Gunakan key Midtrans Production, URL HTTPS permanen, serta Payment Notification URL production.
- Konfigurasikan SMTP/transaksional email dan aktifkan verifikasi email bila diwajibkan oleh kebijakan aplikasi.
- Jalankan scheduler melalui cron/server scheduler setiap menit, misalnya:

  ~~~cron
  * * * * * cd /path/to/Caruna && php artisan schedule:run >> /dev/null 2>&1
  ~~~

- Jalankan queue worker bila aplikasi mulai memakai job asynchronous.
- Set cookie session aman untuk HTTPS, lakukan cache konfigurasi/route/view, dan batasi akses database.
- Terapkan kebijakan retensi, perlindungan akses, serta enkripsi yang sesuai untuk data kesehatan dan data pribadi.

## Lisensi

Proyek ini dibuat untuk kebutuhan akademik. Tentukan lisensi proyek sebelum dipublikasikan atau digunakan di luar konteks tersebut.
