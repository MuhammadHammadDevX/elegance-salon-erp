@echo off
title Elegance Salon Karachi - Development Server
color 0E
echo ==============================================================
echo       ELEGANCE SALON KARACHI - LUXURY MANAGEMENT SYSTEM
echo ==============================================================
echo.
echo [1/3] Checking PHP Installation...
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP is not found in your system PATH.
    echo Please install XAMPP or PHP 8.x and add php to your environment PATH.
    pause
    exit /b 1
)

echo [2/3] Running Quick System Diagnostics...
php test-suite.php
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [WARNING] Diagnostics found database or configuration issues.
    echo Make sure MySQL is running and database 'salon' is imported from Database/salon.sql.
    echo.
)

echo.
echo [3/3] Starting Local Development Server on http://localhost:8000 ...
echo Press Ctrl+C in this terminal window to stop the server at any time.
echo.

start "" "http://localhost:8000"
php -S localhost:8000
