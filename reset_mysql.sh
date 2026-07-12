#!/bin/bash
# Reset password root MySQL + buat database & user untuk dashboard NCR.
# Jalankan: sudo bash reset_mysql.sh
# Server MySQL resmi Oracle di /usr/local/mysql (datadir /usr/local/mysql/data),
# dikelola launchd com.oracle.oss.mysql.mysqld (punya KeepAlive -> harus di-bootout).

set -u
BASE=/usr/local/mysql
BIN=$BASE/bin
SOCK=/tmp/mysql.sock
PLIST=/Library/LaunchDaemons/com.oracle.oss.mysql.mysqld.plist
LABEL=system/com.oracle.oss.mysql.mysqld
ROOT_PASS='Wbn@Root2026'
NCR_PASS='Wbn@Ncr2026'

echo "==> [1/5] Mengeluarkan daemon MySQL dari launchd (bootout)..."
launchctl bootout "$LABEL" 2>/dev/null || launchctl unload -w "$PLIST" 2>/dev/null || true
sleep 2
# pastikan benar-benar tidak ada mysqld yang hidup (launchd tak akan respawn lagi)
pkill -x mysqld 2>/dev/null || true
for i in $(seq 1 20); do pgrep -x mysqld >/dev/null || break; sleep 1; done
if pgrep -x mysqld >/dev/null; then
  echo "    !! mysqld masih hidup (launchd respawn?). Coba paksa stop..."
  pkill -9 -x mysqld 2>/dev/null || true
  sleep 3
fi
echo "    mysqld aktif sekarang: $(pgrep -x mysqld | tr '\n' ' ' || echo none)"

echo "==> [2/5] Menjalankan MySQL mode skip-grant-tables..."
"$BIN/mysqld_safe" --skip-grant-tables --user=_mysql --socket="$SOCK" >/tmp/mysql_reset.log 2>&1 &
for i in $(seq 1 30); do "$BIN/mysqladmin" --socket="$SOCK" ping >/dev/null 2>&1 && break; sleep 1; done
if ! "$BIN/mysqladmin" --socket="$SOCK" ping >/dev/null 2>&1; then
  echo "    !! Gagal start skip-grant. Isi /tmp/mysql_reset.log:"; tail -20 /tmp/mysql_reset.log; exit 1
fi
echo "    skip-grant aktif (pid: $(pgrep -x mysqld | tr '\n' ' '))"

echo "==> [3/5] Set ulang root + buat ncr_dashboard / ncr_user (verbose)..."
"$BIN/mysql" --socket="$SOCK" -uroot <<SQL
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '${ROOT_PASS}';
CREATE DATABASE IF NOT EXISTS ncr_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'ncr_user'@'localhost' IDENTIFIED BY '${NCR_PASS}';
CREATE USER IF NOT EXISTS 'ncr_user'@'127.0.0.1' IDENTIFIED BY '${NCR_PASS}';
ALTER USER 'ncr_user'@'localhost' IDENTIFIED BY '${NCR_PASS}';
ALTER USER 'ncr_user'@'127.0.0.1' IDENTIFIED BY '${NCR_PASS}';
GRANT ALL PRIVILEGES ON ncr_dashboard.* TO 'ncr_user'@'localhost';
GRANT ALL PRIVILEGES ON ncr_dashboard.* TO 'ncr_user'@'127.0.0.1';
FLUSH PRIVILEGES;
SELECT user, host, plugin FROM mysql.user WHERE user IN ('root','ncr_user') ORDER BY user, host;
SQL
echo "    (exit code SQL: $?)"

echo "==> [4/5] Mematikan instance skip-grant-tables..."
"$BIN/mysqladmin" --socket="$SOCK" -uroot -p"${ROOT_PASS}" shutdown 2>/dev/null \
  || pkill -x mysqld 2>/dev/null || true
for i in $(seq 1 20); do pgrep -x mysqld >/dev/null || break; sleep 1; done
sleep 2

echo "==> [5/5] Menjalankan MySQL normal lagi (launchd bootstrap)..."
launchctl bootstrap system "$PLIST" 2>/dev/null || launchctl load -w "$PLIST" 2>/dev/null || true
for i in $(seq 1 30); do "$BIN/mysqladmin" --socket="$SOCK" ping >/dev/null 2>&1 && break; sleep 1; done

echo
echo "==> SELESAI. root=${ROOT_PASS} ; ncr_user=${NCR_PASS}"
echo "    Verifikasi (via socket / localhost):"
"$BIN/mysql" -uncr_user -p"${NCR_PASS}" --socket="$SOCK" -e "SELECT 'ncr_user CONNECT OK (socket)' AS status; SHOW DATABASES LIKE 'ncr_dashboard';" 2>&1 | grep -v "insecure"
echo "    Verifikasi (via TCP 127.0.0.1):"
"$BIN/mysql" -uncr_user -p"${NCR_PASS}" -h127.0.0.1 -P3306 -e "SELECT 'ncr_user CONNECT OK (tcp)' AS status;" 2>&1 | grep -v "insecure"
