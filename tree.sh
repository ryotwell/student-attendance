#!/usr/bin/env bash

# =============================================================================
# Script: export_php_code.sh
# Deskripsi: Menggabungkan semua file .php dari folder Migrations & Models
#            menjadi file .txt dengan header untuk memudahkan review.
# Cara pakai: ./export_php_code.sh
# =============================================================================

set -e # Hentikan script jika ada error

# --- KONFIGURASI (ubah jika struktur folder Anda beda) ---
MIGRATIONS_PATH="database/migrations"
MODELS_PATH="app/Models"   # Jika Laravel versi lama, ganti jadi "app"
OUTPUT_DIR="./exports"
# ----------------------------------------------------------

# Buat folder output jika belum ada
mkdir -p "$OUTPUT_DIR"

# Fungsi untuk mengekspor satu direktori
export_directory() {
    local SOURCE_DIR="$1"
    local OUTPUT_FILE="$2"
    local LABEL="$3"

    if [ ! -d "$SOURCE_DIR" ]; then
        echo "❌ Lewati: Direktori '$SOURCE_DIR' tidak ditemukan."
        return 1
    fi

    echo "📁 Mengekspor $LABEL dari '$SOURCE_DIR' -> '$OUTPUT_FILE'"

    # Kosongkan file output
    > "$OUTPUT_FILE"

    # Cari semua file .php (rekursif), urutkan, lalu tulis dengan header
    find "$SOURCE_DIR" -type f -name "*.php" | sort | while read -r FILE; do
        {
            echo "==================== $FILE ===================="
            cat "$FILE"
            echo -e "\n" # Beri jarak antar file
        } >> "$OUTPUT_FILE"
    done

    # Laporan hasil
    if [ -s "$OUTPUT_FILE" ]; then
        echo "✅ $LABEL berhasil diekspor. Jumlah baris: $(wc -l < "$OUTPUT_FILE")"
    else
        echo "⚠️  $LABEL diekspor tetapi file output kosong."
    fi
}

# ===================== EKSEKUSI =====================
echo "====================================="
echo "🚀 Memulai Ekspor Kode PHP"
echo "====================================="

# 1. Ekspor Migrations
export_directory "$MIGRATIONS_PATH" "$OUTPUT_DIR/migrations.txt" "Migrations"

# 2. Ekspor Models
export_directory "$MODELS_PATH" "$OUTPUT_DIR/models.txt" "Models"

# 3. Gabungkan keduanya (opsional)
COMBINED_FILE="$OUTPUT_DIR/combined_migrations_models.txt"
if [ -f "$OUTPUT_DIR/migrations.txt" ] && [ -f "$OUTPUT_DIR/models.txt" ]; then
    echo "🔗 Membuat file gabungan: $COMBINED_FILE"
    {
        echo "====================================================="
        echo "            GABUNGAN MIGRATIONS & MODELS             "
        echo "====================================================="
        echo ""
        echo "================= MIGRATIONS =================="
        cat "$OUTPUT_DIR/migrations.txt"
        echo ""
        echo "================= MODELS ======================"
        cat "$OUTPUT_DIR/models.txt"
    } > "$COMBINED_FILE"
    echo "✅ File gabungan berhasil dibuat."
fi

echo "====================================="
echo "✅ Ekspor selesai!"
echo "📂 File output berada di folder: $OUTPUT_DIR"
ls -lah "$OUTPUT_DIR" | grep '.txt'
echo "====================================="