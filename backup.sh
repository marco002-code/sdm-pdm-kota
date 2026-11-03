#!/usr/bin/env bash
# ==============================================================================
# SKRIP PENCADANGAN (BACKUP) DATABASE OTOMATIS
# SIM SDM PDM KOTA YOGYAKARTA
# ==============================================================================
set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$PROJECT_DIR/storage/backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

mkdir -p "$BACKUP_DIR"

echo "📦 Memulai backup basis data [Timestamp: $TIMESTAMP]..."

# 1. Backup Database SQLite (Dengan metode .backup atomik jika sqlite3 tersedia)
if [ -f "$PROJECT_DIR/database/database.sqlite" ]; then
    DEST_FILE="$BACKUP_DIR/sdm_db_$TIMESTAMP.sqlite"
    if command -v sqlite3 &> /dev/null; then
        sqlite3 "$PROJECT_DIR/database/database.sqlite" ".backup '$DEST_FILE'"
    else
        cp "$PROJECT_DIR/database/database.sqlite" "$DEST_FILE"
    fi
    gzip -f "$DEST_FILE"
    echo "✔ Backup SQLite berhasil disimpan: ${DEST_FILE}.gz"
fi

# 2. Pembersihan Backup Lama (> 14 hari) untuk Menghemat Ruang Disk Server
find "$BACKUP_DIR" -type f -name "*.gz" -mtime +14 -exec rm {} \; 2>/dev/null || true
echo "✔ Pembersihan backup lama (>14 hari) selesai."

