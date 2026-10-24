#!/bin/bash

# =============================================================
# Export data migrasi dari absensi_f1 ke absen-web-rasi
# Tables: departments, users, karyawans
# =============================================================

DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_NAME="absensi_f1"
DB_USER="root"
DB_PASS=""

OUTPUT_FILE="migrasi_rasi_$(date +%Y%m%d_%H%M%S).sql"

echo "Mulai export dari database: $DB_NAME ..."

mysqldump \
  -h "$DB_HOST" \
  -P "$DB_PORT" \
  -u "$DB_USER" \
  ${DB_PASS:+-p"$DB_PASS"} \
  --no-tablespaces \
  --skip-add-drop-table \
  --no-create-info \
  --complete-insert \
  --single-transaction \
  "$DB_NAME" departments users karyawans \
  | sed '1s/^/SET FOREIGN_KEY_CHECKS=0;\n/' \
  | sed -e '$aSET FOREIGN_KEY_CHECKS=1;' \
  > "$OUTPUT_FILE"

if [ $? -eq 0 ]; then
  echo "Export berhasil: $OUTPUT_FILE"
  echo "Ukuran file: $(du -sh $OUTPUT_FILE | cut -f1)"
else
  echo "Export GAGAL. Cek koneksi database."
  exit 1
fi
