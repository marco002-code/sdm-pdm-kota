#!/usr/bin/env bash
# ==============================================================================
# SKRIP PENCADANGAN (BACKUP) DATABASE OTOMATIS (MySQL & SQLite)
# SIM SDM PDM KOTA YOGYAKARTA
# ==============================================================================
set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$PROJECT_DIR/storage/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

mkdir -p "$BACKUP_DIR"

echo "📦 Memulai backup basis data [Timestamp: $TIMESTAMP]..."

# 1. Backup Basis Data MySQL (Jika .env menggunakan koneksi mysql)
if [ -f "$PROJECT_DIR/.env" ] && grep -q "DB_CONNECTION=mysql" "$PROJECT_DIR/.env" 2>/dev/null; then
    DB_NAME=$(grep "^DB_DATABASE=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ')
    DB_USER=$(grep "^DB_USERNAME=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ')
    DB_PASS=$(grep "^DB_PASSWORD=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ')
    DB_HOST=$(grep "^DB_HOST=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ' || echo "127.0.0.1")
    DB_PORT=$(grep "^DB_PORT=" "$PROJECT_DIR/.env" | cut -d '=' -f2- | tr -d '"' | tr -d "'" | tr -d ' ' || echo "3306")

    DEST_SQL="$BACKUP_DIR/sdm_mysql_${DB_NAME}_$TIMESTAMP.sql"
    if command -v mysqldump &> /dev/null; then
        if [ -n "$DB_PASS" ]; then
            mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$DEST_SQL" 2>/dev/null || true
        else
            mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "$DB_USER" "$DB_NAME" > "$DEST_SQL" 2>/dev/null || true
        fi
        if [ -s "$DEST_SQL" ]; then
            gzip -f "$DEST_SQL"
            echo "✔ Backup MySQL berhasil disimpan: ${DEST_SQL}.gz"
        fi
    fi
fi

# 2. Backup Basis Data SQLite (Jika file sqlite tersedia)
if [ -f "$PROJECT_DIR/database/database.sqlite" ]; then
    DEST_FILE="$BACKUP_DIR/sdm_db_$TIMESTAMP.sqlite"
    if command -v sqlite3 &> /dev/null; then
        sqlite3 "$PROJECT_DIR/database/database.sqlite" ".backup '$DEST_FILE'" 2>/dev/null || cp "$PROJECT_DIR/database/database.sqlite" "$DEST_FILE"
    else
        cp "$PROJECT_DIR/database/database.sqlite" "$DEST_FILE"
    fi
    gzip -f "$DEST_FILE"
    echo "✔ Backup SQLite berhasil disimpan: ${DEST_FILE}.gz"
fi

# 3. Pembersihan Backup Lama (> 14 hari) untuk Menghemat Ruang Disk Server
find "$BACKUP_DIR" -type f -name "*.gz" -mtime +14 -exec rm {} \; 2>/dev/null || true
echo "✔ Pembersihan backup lama (>14 hari) selesai."
