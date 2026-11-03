# 🏛️ PANDUAN DEPLOYMENT SIM SDM PDM KOTA YOGYAKARTA
## Khusus Lingkungan Debian + Nginx + HestiaCP

Panduan ini disusun khusus untuk deployment aplikasi **SIM SDM PDM Kota Yogyakarta** pada server **Debian (Debian 11 Bullseye / Debian 12 Bookworm)** yang menggunakan **Hestia Control Panel (HestiaCP)** dengan web server **Nginx Standalone (PHP-FPM)** atau **Nginx + Apache**.

---

## 1. Memahami Arsitektur HestiaCP

Di HestiaCP, terdapat beberapa aturan penting yang berbeda dari server Ubuntu/Debian polos:
1. **User Ownership**: File web dimiliki oleh user HestiaCP bersangkutan (misal: `admin:admin` atau `sdmpdm:sdmpdm`), **BUKAN** `www-data:www-data`. PHP-FPM di HestiaCP berjalan di bawah user tersebut.
2. **Struktur Direktori Default**:
   ```text
   /home/<user>/web/<domain>/
   ├── conf/               # Konfigurasi Nginx/Apache otomatis dari HestiaCP
   ├── logs/               # Log akses & error domain
   └── public_html/        # Root direktori web publik default
   ```
3. **Versi PHP**: HestiaCP mendukung Multi-PHP (`/usr/bin/php8.2`, `/usr/bin/php8.3`). Pastikan ekstensi `php8.2-sqlite3`, `php8.2-gd`, `php8.2-curl`, `php8.2-zip`, `php8.2-mbstring`, `php8.2-xml` terpasang di Debian.

---

## 2. Persiapan Server Debian & Paket PHP

Login via SSH ke server Debian Anda sebagai `root`:
```bash
# Pastikan ekstensi PHP yang dibutuhkan Laravel dan SQLite/GD terpasang (contoh PHP 8.2)
apt update && apt install -y php8.2-sqlite3 php8.2-gd php8.2-curl php8.2-zip php8.2-mbstring \
    php8.2-xml php8.2-bcmath sqlite3 git unzip
```

---

## 3. Menambahkan Domain di Panel HestiaCP

1. Login ke panel HestiaCP Anda (`https://IP-SERVER:8083`).
2. Masuk ke user yang dituju (misal user `admin` atau buat user baru `sdmpdm`).
3. Klik menu **WEB** -> Klik **Add Web Domain**.
4. Masukkan nama domain: `sdm.pdmkotayogya.or.id` (atau domain/subdomain yang Anda gunakan).
5. Klik **Advanced Options**:
   - **Backend Template PHP-FPM**: Pilih `PHP-8.2` (atau `PHP-8.3`).
   - Centang **Enable SSL support**.
   - Centang **Use Let's Encrypt to obtain SSL certificate**.
   - Centang **Enable automatic HTTP-to-HTTPS redirection**.
6. Klik **Save**. HestiaCP akan otomatis mendaftarkan sertifikat SSL gratis Let's Encrypt dan mengaktifkan HTTPS!

---

## 4. Menghubungkan Nginx HestiaCP ke Folder `public` Laravel

Laravel mengharuskan web server membaca folder `public/`, bukan root proyek. Anda dapat memilih salah satu dari dua cara berikut:

### OPSI A (Paling Mudah & Praktis - Menggunakan Symlink):
*Metode ini tidak memerlukan akses root dan tidak mengubah template sistem HestiaCP.*

Login via SSH sebagai user HestiaCP (misal user `admin` atau `sdmpdm`):
```bash
# Ganti <user> dan <domain> sesuai data Anda
USER="admin"
DOMAIN="sdm.pdmkotayogya.or.id"

cd /home/$USER/web/$DOMAIN/

# 1. Clone source code repositori ke folder aplikasi
git clone https://github.com/marco002-code/sdm-pdm-kota.git app

# 2. Hapus folder public_html default bawaan HestiaCP (hanya berisi file index dummy)
rm -rf public_html

# 3. Buat symlink dari app/public ke public_html
ln -s app/public public_html
```
*Dengan metode ini, HestiaCP tetap membaca `public_html`, namun isinya langsung mengarah ke `app/public` milik Laravel.*

---

### OPSI B (Template Native HestiaCP "laravel"):
*Jika Anda ingin memilih template "laravel" langsung di dropdown Web UI HestiaCP:*

Sebagai user `root`:
```bash
# Salin template custom dari repositori ke direktori template HestiaCP
cp /home/<user>/web/<domain>/app/hestiacp/laravel.tpl /usr/local/hestia/data/templates/web/nginx/php-fpm/
cp /home/<user>/web/<domain>/app/hestiacp/laravel.stpl /usr/local/hestia/data/templates/web/nginx/php-fpm/

# Terapkan template via CLI HestiaCP atau via Web GUI
v-change-web-domain-tpl <user> <domain> laravel
```
Lalu tempatkan file aplikasi langsung di `/home/<user>/web/<domain>/public_html/`.

---

## 5. Konfigurasi Environment & Database (.env)

Masuk ke direktori aplikasi Laravel Anda:
```bash
cd /home/<user>/web/<domain>/app  # (Atau public_html jika Opsi B)

# 1. Salin template .env.beta
cp .env.beta .env

# 2. Edit konfigurasi .env
nano .env
```

Sesuaikan baris-baris berikut:
```ini
APP_NAME="SIM SDM PDM Kota Yogyakarta"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sdm-pdm.muhijunior.id

# Konfigurasi Basis Data MySQL HestiaCP
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adminmlc_sdm_db
DB_USERNAME=adminmlc_sdm
DB_PASSWORD="UGyIWd@Dbx7Qad5r"

QUEUE_CONNECTION=database
SESSION_DRIVER=file
CACHE_STORE=file
```

Generate application key:
```bash
/usr/bin/php8.2 artisan key:generate --force
```

---

## 6. Jalankan Skrip Deployment Otomatis

Jalankan skrip deployment bawaan yang telah dioptimalkan:
```bash
chmod +x deploy.sh backup.sh
./deploy.sh
```

Skrip ini akan secara otomatis:
- Menjalankan backup database.
- Menjalankan composer install optimized (`--no-dev`).
- Mengompilasi aset Vite (jika node/npm terpasang).
- Menjalankan migrasi database (`php artisan migrate --force`).
- Menghubungkan storage symlink (`php artisan storage:link`).
- Mengoptimasi cache Laravel (`php artisan optimize`).
- Memperbarui izin direktori (`chmod 775 storage bootstrap/cache database`).
- Memeriksa kesiapan sistem via `php artisan app:preflight`.

---

## 7. Setup Cron Job di Panel HestiaCP (GUI)

HestiaCP menyediakan antarmuka Cron yang sangat mudah:
1. Login ke panel HestiaCP -> Klik menu **CRON** di bagian atas.
2. Klik **Add Cron Job**.
3. **Cron 1: Laravel Task Scheduler**
   - Minute: `*`
   - Hour: `*`
   - Day: `*`
   - Month: `*`
   - Day of week: `*`
   - Command:
     ```bash
     /usr/bin/php8.2 /home/<user>/web/<domain>/app/artisan schedule:run >> /dev/null 2>&1
     ```
   - Klik **Save**.
4. **Cron 2: Backup Otomatis Basis Data (Setiap Jam 02:00 Dini Hari)**
   - Minute: `0`
   - Hour: `2`
   - Day: `*`
   - Month: `*`
   - Day of week: `*`
   - Command:
     ```bash
     /bin/bash /home/<user>/web/<domain>/app/backup.sh >> /home/<user>/web/<domain>/app/storage/logs/backup.log 2>&1
     ```
   - Klik **Save**.

---

## 8. Setup Background Queue Worker (Systemd di Debian)

Agar proses asinkron (kalkulasi nilai, antrian nomor NPM, dan audit) berjalan mulus di latar belakang:

1. Buat file service systemd sebagai `root`:
   ```bash
   sudo nano /etc/systemd/system/sdm-queue-worker.service
   ```
2. Isi konfigurasinya (sesuaikan `<user>` dan `<domain>`):
   ```ini
   [Unit]
   Description=SIM SDM PDM Kota Yogyakarta - Background Queue Worker
   After=network.target

   [Service]
   Type=simple
   User=<user>
   Group=<user>
   WorkingDirectory=/home/<user>/web/<domain>/app
   ExecStart=/usr/bin/php8.2 /home/<user>/web/<domain>/app/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
   Restart=always
   RestartSec=5
   StandardOutput=append:/home/<user>/web/<domain>/app/storage/logs/queue-worker.log
   StandardError=append:/home/<user>/web/<domain>/app/storage/logs/queue-worker-error.log

   [Install]
   WantedBy=multi-user.target
   ```
3. Aktifkan dan jalankan worker:
   ```bash
   sudo systemctl daemon-reload
   sudo systemctl enable --now sdm-queue-worker
   sudo systemctl status sdm-queue-worker
   ```

---

## 9. Prosedur Update Rutin di HestiaCP

Setiap kali ada pembaruan kode di GitHub, Anda cukup menjalankan perintah berikut via SSH:
```bash
cd /home/<user>/web/<domain>/app
git pull origin master
./deploy.sh
```

Aplikasi akan otomatis ter-update tanpa downtime dan dengan pencadangan basis data otomatis.

