#!/bin/bash
# Jalankan dashboard (memakai PHP 8.3 dari Homebrew)
PHP=/usr/local/opt/php@8.3/bin/php
cd "$(dirname "$0")"
echo "Menjalankan server di http://127.0.0.1:8000 ..."
exec "$PHP" artisan serve
