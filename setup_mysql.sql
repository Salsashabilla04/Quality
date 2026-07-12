-- Setup database untuk Dashboard Monitoring Complaint & NCR
-- Jalankan dengan user root MySQL Anda:
--   mysql -uroot -p < setup_mysql.sql

CREATE DATABASE IF NOT EXISTS ncr_dashboard
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'ncr_user'@'localhost' IDENTIFIED BY 'Wbn@Ncr2026';
CREATE USER IF NOT EXISTS 'ncr_user'@'127.0.0.1' IDENTIFIED BY 'Wbn@Ncr2026';

GRANT ALL PRIVILEGES ON ncr_dashboard.* TO 'ncr_user'@'localhost';
GRANT ALL PRIVILEGES ON ncr_dashboard.* TO 'ncr_user'@'127.0.0.1';

FLUSH PRIVILEGES;
