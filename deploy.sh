#!/usr/bin/env bash
# ==============================================================================
# SKRIP AUTOMATISASI DEPLOYMENT RESILIEN VERSI BETA / PRODUCTION
# SIM SDM PDM KOTA YOGYAKARTA (Debian, Nginx, HestiaCP & Linux Server)
# ==============================================================================
set -e

# Warna teks output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# Trap handler: Memberi instruksi pemulihan jika terjadi error di tengah proses
cleanup() {
    local exit_code=$?
    if [ $exit_code -ne 0 ]; then
        echo -e "\n${RED}======================================================================${NC}"
        echo -e "${RED}❌ DEPLOYMENT TERHENTI DENGAN KESALAHAN (Exit Code: $exit_code)${NC}"
        echo -e "${YELLOW}ℹ️  Aplikasi kemungkinan masih dalam Mode Pemeliharaan (Maintenance Mode).${NC}"
        echo -e "${YELLOW}ℹ️  Jalankan perintah berikut untuk mengaktifkan kembali aplikasi:${NC}"
        echo -e "    ${GREEN}$PHP_BIN artisan up${NC}"
        echo -e "${RED}======================================================================${NC}"
    fi
}
trap cleanup EXIT

echo -e "${BLUE}======================================================================${NC}"
echo -e "${BLUE}🚀 MEMULAI PROSES DEPLOYMENT SIM SDM PDM KOTA YOGYAKARTA${NC}"
echo -e "${BLUE}======================================================================${NC}"

# ------------------------------------------------------------------------------
# 1. Deteksi & Validasi PHP Binary (Multi-PHP Support di HestiaCP / Debian)
# ------------------------------------------------------------------------------
if [ -z "$PHP_BIN" ]; then
    if command -v php &> /dev/null && php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);" 2>/dev/null; then
        PHP_BIN="php"
    elif command -v php8.2 &> /dev/null && php8.2 -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);" 2>/dev/null; then
        PHP_BIN="php8.2"
    elif command -v php8.3 &> /dev/null && php8.3 -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);" 2>/dev/null; then
        PHP_BIN="php8.3"
    elif command -v php8.4 &> /dev/null; then
        PHP_BIN="php8.4"
    else
        PHP_BIN="php"
    fi
fi

PHP_VER=$($PHP_BIN -r "echo PHP_VERSION;" 2>/dev/null || echo "0")
echo -e "${YELLOW}🐘 Menggunakan PHP Binary: ${GREEN}$PHP_BIN${NC} (Versi: ${GREEN}$PHP_VER${NC})"

if ! $PHP_BIN -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);" 2>/dev/null; then
    echo -e "${RED}❌ PHP version ($PHP_VER) tidak memenuhi syarat minimum (>= 8.2).${NC}"
    echo -e "${YELLOW}ℹ️  Anda dapat menentukan binary PHP secara spesifik, contoh:${NC}"
    echo -e "    PHP_BIN=php8.2 ./deploy.sh"
    exit 1
fi

# Deteksi Composer
if command -v composer &> /dev/null; then
    COMPOSER_CMD="composer"
elif [ -f "$PROJECT_DIR/composer.phar" ]; then
    COMPOSER_CMD="$PHP_BIN $PROJECT_DIR/composer.phar"
elif [ -f "/usr/local/bin/composer" ]; then
    COMPOSER_CMD="/usr/local/bin/composer"
else
    COMPOSER_CMD="composer"
fi

# ------------------------------------------------------------------------------
# 2. Validasi Direktori Proyek & Keberadaan Berkas 'artisan'
# ------------------------------------------------------------------------------
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo -e "${RED}❌ Berkas 'artisan' tidak ditemukan di: $PROJECT_DIR${NC}"
    FOUND_ARTISAN=$(find "$PROJECT_DIR" -maxdepth 2 -name "artisan" 2>/dev/null | head -n 1)
    if [ -n "$FOUND_ARTISAN" ]; then
        SUGGESTED_DIR="$(dirname "$FOUND_ARTISAN")"
        echo -e "${YELLOW}💡 Ditemukan berkas 'artisan' di subfolder: ${GREEN}$SUGGESTED_DIR${NC}"
        echo -e "${YELLOW}👉 Silakan masuk ke subfolder tersebut dan jalankan deploy:${NC}"
        echo -e "    cd $SUGGESTED_DIR && ./deploy.sh"
    else
        echo -e "${YELLOW}💡 Pastikan seluruh berkas repositori (termasuk berkas 'artisan') telah di-clone/disalin ke direktori ini.${NC}"
    fi
    exit 1
fi

# ------------------------------------------------------------------------------
# 3. Inisialisasi Lingkungan Awal (Self-Healing jika .env belum ada atau perlu konfigurasi MySQL)
# ------------------------------------------------------------------------------
if [ ! -f .env ]; then
    echo -e "${YELLOW}⚠️  Berkas .env belum ditemukan. Menginisialisasi dari template .env.beta...${NC}"
    if [ -f .env.beta ]; then
        cp .env.beta .env
    elif [ -f .env.example ]; then
        cp .env.example .env
    fi
    echo -e "${YELLOW}🔑 Menghasilkan APP_KEY unik...${NC}"
    $PHP_BIN artisan key:generate --force
fi

# Pastikan kredensial basis data MySQL produksi (adminmlc_sdm_db) disinkronkan ke .env
if [ -f .env ] && grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; then
    echo -e "${YELLOW}🔄 Mengalihkan konfigurasi basis data .env ke MySQL (adminmlc_sdm_db)...${NC}"
    sed -i 's/^DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
    
    if grep -q "^DB_DATABASE=" .env; then
        sed -i 's|^DB_DATABASE=.*|DB_DATABASE=adminmlc_sdm_db|' .env
    else
        echo "DB_DATABASE=adminmlc_sdm_db" >> .env
    fi
    
    if grep -q "^DB_USERNAME=" .env; then
        sed -i 's/^DB_USERNAME=.*/DB_USERNAME=adminmlc_sdm/' .env
    else
        echo "DB_USERNAME=adminmlc_sdm" >> .env
    fi
    
    if grep -q "^DB_PASSWORD=" .env; then
        sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD="UGyIWd@Dbx7Qad5r"|' .env
    else
        echo 'DB_PASSWORD="UGyIWd@Dbx7Qad5r"' >> .env
    fi
    
    if grep -q "^DB_HOST=" .env; then
        sed -i 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' .env
    else
        echo "DB_HOST=127.0.0.1" >> .env
    fi
    
    if grep -q "^DB_PORT=" .env; then
        sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
    else
        echo "DB_PORT=3306" >> .env
    fi
fi

# Pastikan seluruh struktur folder storage tersedia
mkdir -p storage/app/public \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/backups \
         bootstrap/cache \
         database

# ------------------------------------------------------------------------------
# 3. Pencadangan (Backup) Otomatis Basis Data Sebelum Pembaruan
# ------------------------------------------------------------------------------
if [ -f "$PROJECT_DIR/backup.sh" ]; then
    echo -e "${YELLOW}📦 [1/8] Menjalankan pencadangan (backup) otomatis...${NC}"
    bash "$PROJECT_DIR/backup.sh" || echo -e "${YELLOW}⚠️ Backup dilewati/gagal, melanjutkan deploy...${NC}"
else
    echo -e "${YELLOW}ℹ️  [1/8] Skrip backup tidak ditemukan, melanjutkan...${NC}"
fi

# ------------------------------------------------------------------------------
# 4. Aktifkan Maintenance Mode (Aman dari interaksi user saat migrasi)
# ------------------------------------------------------------------------------
echo -e "${YELLOW}🔒 [2/8] Mengaktifkan Mode Pemeliharaan (Maintenance Mode)...${NC}"
$PHP_BIN artisan down --retry=60 --secret="pdm-beta-bypass" || true

# ------------------------------------------------------------------------------
# 5. Update Dependencies Composer (No-Dev, Optimize Autoloader)
# ------------------------------------------------------------------------------
echo -e "${YELLOW}📦 [3/8] Menginstal dependensi PHP (Composer Optimize)...${NC}"
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-req=php+

# ------------------------------------------------------------------------------
# 6. Kompilasi Aset Frontend (Vite)
# ------------------------------------------------------------------------------
if command -v npm &> /dev/null; then
    echo -e "${YELLOW}🎨 [4/8] Mengompilasi aset frontend (Vite Build)...${NC}"
    npm run build
else
    if [ -f "public/build/manifest.json" ]; then
        echo -e "${GREEN}✔ [4/8] Node/NPM tidak terpasang di server, namun aset terkompilasi (public/build) siap digunakan.${NC}"
    else
        echo -e "${YELLOW}⚠️  [4/8] Aset frontend belum terkompilasi dan npm tidak ditemukan. Silakan jalankan 'npm run build' secara lokal lalu upload folder public/build.${NC}"
    fi
fi

# ------------------------------------------------------------------------------
# 7. Eksekusi Migrasi Database & Seeder Otomatis jika Database Kosong
# ------------------------------------------------------------------------------
echo -e "${YELLOW}🗄️  [5/8] Menjalankan migrasi database...${NC}"
$PHP_BIN artisan migrate --force

# Cek apakah master data unit kerja kosong; jika kosong, jalankan seeder otomatis
UNITS_COUNT=$($PHP_BIN -r "
    require __DIR__.'/vendor/autoload.php';
    \$app = require_once __DIR__.'/bootstrap/app.php';
    \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    try {
        echo \App\Models\Unit::count();
    } catch (\Throwable \$e) {
        echo '0';
    }
" 2>/dev/null || echo "0")

if [ "$UNITS_COUNT" -eq "0" ]; then
    echo -e "${YELLOW}🌱 Basis data masih kosong. Menjalankan seeder awal (76 Unit AUM & Akun)...${NC}"
    $PHP_BIN artisan db:seed --force
fi

# ------------------------------------------------------------------------------
# 8. Pasang Symbolic Link Storage
# ------------------------------------------------------------------------------
echo -e "${YELLOW}🔗 [6/8] Memeriksa storage symbolic link...${NC}"
$PHP_BIN artisan storage:link || true

# ------------------------------------------------------------------------------
# 9. Optimasi Framework Cache (Config, Route, View, Event)
# ------------------------------------------------------------------------------
echo -e "${YELLOW}⚡ [7/8] Mengoptimalkan cache sistem (Laravel Optimize)...${NC}"
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan optimize

# Restart Antrian Latar Belakang (Queue Worker)
$PHP_BIN artisan queue:restart || true

# ------------------------------------------------------------------------------
# 10. Setel Hak Akses / Permissions Direktori
# ------------------------------------------------------------------------------
echo -e "${YELLOW}🛡️  [8/8] Memperbarui izin direktori (permissions)...${NC}"
chmod -R 775 storage bootstrap/cache database 2>/dev/null || true
if [ -f database/database.sqlite ]; then
    chmod 664 database/database.sqlite 2>/dev/null || true
fi

# ------------------------------------------------------------------------------
# 11. Buka Kembali Aplikasi (Exit Maintenance Mode)
# ------------------------------------------------------------------------------
echo -e "${GREEN}🔓 Membuka kembali aplikasi ke publik...${NC}"
$PHP_BIN artisan up

# ------------------------------------------------------------------------------
# 12. Jalankan Pre-Flight Check
# ------------------------------------------------------------------------------
echo -e "${BLUE}======================================================================${NC}"
$PHP_BIN artisan app:preflight

echo -e "${GREEN}======================================================================${NC}"
echo -e "${GREEN}✅ DEPLOYMENT SIM SDM PDM KOTA YOGYAKARTA SELESAI DENGAN SUKSES!${NC}"
echo -e "${GREEN}======================================================================${NC}"
