@echo off
cls
echo ======================================
echo   BeFit Project - Composer Setup
echo ======================================

:: Check if composer is available
where composer >nul 2>&1
IF %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] Composer is not installed or not added to PATH.
    echo Download it from: https://getcomposer.org/download/
    pause
    exit /b
)

:: Check if composer.json exists
if not exist "composer.json" (
    echo.
    echo [ERROR] composer.json not found in this folder:
    echo %CD%
    echo Please run this script from your project root folder.
    pause
    exit /b
)

:: Run composer install
echo.
echo Installing dependencies with Composer...
composer install

IF %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCESS] All Composer dependencies installed successfully!
    echo.
    echo This window will close in 10 seconds. Press any key to close it now.
    timeout /t 10 >nul
) ELSE (
    echo
