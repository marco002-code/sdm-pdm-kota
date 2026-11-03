# 🚀 PANDUAN DEPLOYMENT VERSI BETA
## Sistem Informasi SDM PDM Kota Yogyakarta

Panduan ini disusun untuk memandu tim IT / Administrator Pimpinan Daerah Muhammadiyah (PDM) Kota Yogyakarta dalam melakukan proses deployment aplikasi SIM SDM versi Beta ke server staging atau production (VPS / Cloud Server berbasis Linux Ubuntu/Debian).

---

## 1. Persyaratan Sistem & Spesifikasi Server

| Komponen | Spesifikasi Minimum / Rekomendasi |
| :--- | :--- |
| **Sistem Operasi** | Ubuntu 22.04 LTS / 24.04 LTS / Debian 12 |
| **CPU & RAM** | 2 vCPU, RAM 2GB (Rekomendasi: 4GB untuk kelancaran antrian & compile) |
| **PHP Runtime** | PHP 8.2 atau PHP 8.3 / 8.4 |
| **Ekstensi PHP Wajib** | `pdo`, `pdo_sqlite` (atau `pdo_mysql`), `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` (atau `imagick`), `curl`, `zip` |
| **Web Server** | Nginx (Sangat direkomendasikan) atau Apache 2.4 |
| **Database** | SQLite (Mode WAL Aktif & Busy Timeout 60s) atau MySQL 8.0 / MariaDB 10.6+ |
| **Node.js & NPM** | Node.js v18+ / v20+ & NPM v9+ (Untuk kompilasi frontend Vite) |
| **Composer** | Composer v2.5+ |

### Perintah Instalasi Paket di Ubuntu/Debian:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx php8.2 php8.2-fpm php8.2-cli php8.2-sqlite3 php8.2-mbstring \
    php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath unzip git sqlite3 certbot python3-certbot-nginx
```

---

## 2. Langkah-Langkah Deployment Awal (Initial Setup)

### Langkah 2.1: Tempatkan Kode Aplikasi di Server
Letakkan repositori aplikasi di direktori `/var/www/sdm-pdm-kota`:
```bash
sudo mkdir -p /var/www/sdm-pdm-kota
sudo chown -R $USER:www-data /var/www/sdm-pdm-kota
cd /var/www/sdm-pdm-kota

# Salin/Clone source code ke direktori ini
git clone <URL_REPOSITORY> .
```

### Langkah 2.2: Konfigurasi File Environment (.env)
Gunakan template `.env.beta` yang telah disiapkan:
```bash
cp .env.beta .env
nano .env
```
Sesuaikan parameter penting berikut:
- `APP_NAME="SIM SDM PDM Kota Yogyakarta"`
- `APP_ENV=production`
- `APP_DEBUG=false` *(Wajib `false` agar informasi sensitif tidak terekspos jika terjadi error)*
- `APP_URL=https://sdm.pdmkotayogya.or.id` *(Ganti dengan domain server Anda)*
- `DB_DATABASE=/var/www/sdm-pdm-kota/database/database.sqlite` *(Pastikan path absolut benar)*

### Langkah 2.3: Instal Dependensi & Generate Application Key
```bash
# Instal dependensi PHP tanpa dev-packages (optimized)
composer install --no-dev --optimize-autoloader

# Generate kunci enkripsi unik
php artisan key:generate --force
```

### Langkah 2.4: Kompilasi Aset Frontend (Vite Build)
```bash
# Instal dependensi JavaScript & build aset CSS/JS
npm ci
npm run build
```

### Langkah 2.5: Inisialisasi Database & Migrasi
```bash
# Jika file database SQLite belum ada:
touch database/database.sqlite

# Jalankan seluruh migrasi database
php artisan migrate --force

# (Opsional) Jalankan Seeder jika basis data awal masih kosong:
php artisan db:seed --force
```

### Langkah 2.6: Tautkan Storage Symlink
```bash
php artisan storage:link
```

### Langkah 2.7: Setel Izin Direktori (Permissions)
Web server (`www-data`) harus memiliki izin tulis ke direktori `storage`, `bootstrap/cache`, dan `database`:
```bash
sudo chown -R www-data:www-data /var/www/sdm-pdm-kota/storage /var/www/sdm-pdm-kota/bootstrap/cache /var/www/sdm-pdm-kota/database
sudo chmod -R 775 /var/www/sdm-pdm-kota/storage /var/www/sdm-pdm-kota/bootstrap/cache /var/www/sdm-pdm-kota/database
sudo chmod 664 /var/www/sdm-pdm-kota/database/database.sqlite
```

### Langkah 2.8: Optimasi Cache Laravel
```bash
php artisan optimize:clear
php artisan optimize
```

### Langkah 2.9: Verifikasi Kesiapan Server (Pre-Flight Check)
Jalankan perintah pemeriksa bawaan untuk memastikan tidak ada konfigurasi yang tertinggal:
```bash
php artisan app:preflight
```
*Pastikan seluruh indikator berstatus `PASS` (Hijau).*

---

## 3. Konfigurasi Web Server (Nginx)

File template konfigurasi Nginx siap pakai tersedia di file `nginx-sdm-beta.conf`.

1. Salin konfigurasi ke direktori Nginx:
   ```bash
   sudo cp nginx-sdm-beta.conf /etc/nginx/sites-available/sdm-pdm-kota.conf
   ```
2. Sesuaikan nama domain (`server_name`) dan socket PHP-FPM di file tersebut:
   ```bash
   sudo nano /etc/nginx/sites-available/sdm-pdm-kota.conf
   ```
3. Aktifkan virtual host:
   ```bash
   sudo ln -s /etc/nginx/sites-available/sdm-pdm-kota.conf /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```
4. Pasang Sertifikat SSL Gratis (HTTPS) dengan Certbot:
   ```bash
   sudo certbot --nginx -d sdm.pdmkotayogya.or.id
   ```

---

## 4. Konfigurasi Background Queue Worker (Systemd)

Sistem antrian asinkron diperlukan agar proses berat (seperti pembuatan batch 50+ NPM, pengiriman notifikasi, atau kalkulasi penilaian massal) berjalan di latar belakang tanpa menghambat antarmuka pengguna admin.

File unit systemd tersedia di `sdm-queue-worker.service`.

1. Pasang service unit ke sistem:
   ```bash
   sudo cp sdm-queue-worker.service /etc/systemd/system/sdm-queue-worker.service
   sudo systemctl daemon-reload
   sudo systemctl enable --now sdm-queue-worker
   ```
2. Periksa status antrian:
   ```bash
   sudo systemctl status sdm-queue-worker
   ```

---

## 5. Konfigurasi Cron Job (Penjadwal Otomatis & Backup)

Buka crontab untuk pengguna web server atau root:
```bash
sudo crontab -e
```
Tambahkan entri berikut:
```bash
# 1. Laravel Task Scheduler (Dijalankan setiap menit)
* * * * * cd /var/www/sdm-pdm-kota && php artisan schedule:run >> /dev/null 2>&1

# 2. Pencadangan (Backup) Otomatis Basis Data Setiap Hari Pukul 02:00 Dini Hari
0 2 * * * cd /var/www/sdm-pdm-kota && bash backup.sh >> storage/logs/backup.log 2>&1
```

---

## 6. Prosedur Deployment Pembaruan Selanjutnya (`./deploy.sh`)

Setiap kali tim pengembang merilis pembaruan atau perbaikan kode baru ke server, cukup jalankan skrip otomatisasi yang telah disediakan:
```bash
cd /var/www/sdm-pdm-kota
git pull origin main
./deploy.sh
```

**Alur yang dieksekusi otomatis oleh `deploy.sh`:**
1. Menjalankan backup database secara atomik ke `storage/backups/`.
2. Mengaktifkan *Maintenance Mode* dengan secret token agar pengguna tidak mengalami error saat update.
3. Menjalankan `composer install --no-dev --optimize-autoloader`.
4. Mengompilasi ulang aset `npm run build`.
5. Menjalankan migrasi `php artisan migrate --force`.
6. Memperbarui framework cache (`php artisan optimize`).
7. Memuat ulang queue worker (`php artisan queue:restart`).
8. Memperbarui izin akses folder.
9. Mematikan *Maintenance Mode* (aplikasi aktif kembali).
10. Menjalankan verifikasi sistem `php artisan app:preflight`.

---

## 7. Prosedur Pemulihan Darurat (Rollback & Restore)

Jika terjadi insiden fatal pada saat update:
1. **Restore Basis Data dari Backup**:
   ```bash
   cd /var/www/sdm-pdm-kota
   ls -la storage/backups/
   
   # Uncompress dan gantikan file database
   gunzip -c storage/backups/sdm_db_YYYYMMDD_HHMMSS.sqlite.gz > database/database.sqlite
   sudo chown www-data:www-data database/database.sqlite
   sudo chmod 664 database/database.sqlite
   ```
2. **Kembalikan Kode ke Commit Sebelumnya**:
   ```bash
   git checkout <COMMIT_SEBELUMNYA>
   ./deploy.sh
   ```

