@echo off
title Badminton BookPlay - Web Application Starter
echo ============================================================
echo      BADMINTON BOOKPLAY - SISTEM BOOKING ONLINE (LARAVEL 11)
echo ============================================================
echo.

echo [1/3] Memeriksa & Menjalankan MySQL Database...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL Service sudah aktif di port 3306.
) else (
    echo [*] Memulai MySQL Server dari Laragon...
    start /B "" "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini"
    timeout /t 3 /nobreak >nul
    echo [OK] MySQL Server berhasil dijalankan.
)
echo.

echo [2/3] Membuka Browser Otomatis...
start http://127.0.0.1:8000
echo.

echo [3/3] Menjalankan Laravel Development Server...
echo Aplikasi dapat diakses di: http://127.0.0.1:8000
echo Tekan CTRL+C di jendela ini jika ingin menghentikan server.
echo ============================================================
echo.

php artisan serve --host=127.0.0.1 --port=8000
pause
