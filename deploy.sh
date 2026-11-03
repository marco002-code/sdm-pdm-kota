#!/usr/bin/env bash
# ==============================================================================
# SKRIP AUTOMATISASI DEPLOYMENT VERSI BETA / PRODUCTION
# SIM SDM PDM KOTA YOGYAKARTA
# ==============================================================================
set -e

# Warna teks output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}======================================================================${NC}"
echo -e "${BLUE}🚀 MEMULAI PROSES DEPLOYMENT SIM SDM PDM KOTA YOGYAKARTA${NC}"
echo -e "${BLUE}======================================================================${NC}"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# 1. Backup Database Sebelum Update
if [ -f "$PROJECT_DIR/backup.sh" ]; then
    echo -e "${YELLOW}📦 [1/8] Menjalankan pencadangan (backup) otomatis...${NC}"
    bash "$PROJECT_DIR/backup.sh" || echo -e "${YELLOW}⚠️ Backup dilewati/gagal, melanjutkan deploy...${NC}"
else
    echo -e "${YELLOW}ℹ️  [1/8] Skrip backup tidak ditemukan, melanjutkan...${NC}"
fi

# 2. Aktifkan Maintenance Mode (Aplikasi aman dari interaksi user saat migrasi)
echo -e "${YELLOW}🔒 [2/8] Mengaktifkan Mode Pemeliharaan (Maintenance Mode)...${NC}"
php artisan down --retry=60 --secret="pdm-beta-bypass" || true

# 3. Update Dependencies Composer (No Dev, Optimized)
echo -e "${YELLOW}📦 [3/8] Menginstal dependensi PHP (Composer Optimize)...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 4. Compile Frontend Assets (Vite)
if command -v npm &> /dev/null; then
    echo -e "${YELLOW}🎨 [4/8] Mengompilasi aset frontend (Vite Build)...${NC}"
    npm run build
else
    echo -e "${YELLOW}ℹ️  [4/8] Node/NPM tidak terdeteksi di server; memastikan folder public/build tersedia...${NC}"
fi

# 5. Eksekusi Migrasi Database (Force Flag untuk Lingkungan Production)
echo -e "${YELLOW}🗄️  [5/8] Menjalankan migrasi database...${NC}"
php artisan migrate --force

# 6. Pasang Symbolic Link Storage
echo -e "${YELLOW}🔗 [6/8] Memeriksa storage symbolic link...${NC}"
php artisan storage:link || true

# 7. Optimasi Framework Cache (Config, Route, View, Event)
echo -e "${YELLOW}⚡ [7/8] Mengoptimalkan cache sistem (Laravel Optimize)...${NC}"
php artisan optimize:clear
php artisan optimize

# Restart Antrian Latar Belakang (Queue Worker)
php artisan queue:restart || true

# 8. Setel Hak Akses / Permissions Direktori
echo -e "${YELLOW}🛡️  [8/8] Memperbarui izin direktori (permissions)...${NC}"
chmod -R 775 storage bootstrap/cache database 2>/dev/null || true
if [ -f database/database.sqlite ]; then
    chmod 664 database/database.sqlite 2>/dev/null || true
fi

# Matikan Mode Pemeliharaan (Aplikasi Aktif Kembali)
echo -e "${GREEN}🔓 Membuka kembali aplikasi ke publik...${NC}"
php artisan up

# Jalankan Pre-Flight Check untuk Memverifikasi Kesehatan Sistem
echo -e "${BLUE}======================================================================${NC}"
php artisan app:preflight

echo -e "${GREEN}======================================================================${NC}"
echo -e "${GREEN}✅ DEPLOYMENT VERSI BETA SELESAI DENGAN SUKSES!${NC}"
echo -e "${GREEN}======================================================================${NC}"

