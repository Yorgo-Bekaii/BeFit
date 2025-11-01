@echo off
:: Create database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS befit_db"

:: Import tables 
echo Checking if dump.sql exists...
dir ..\database\dump.sql
pause
mysql -u root befit_db < ..\database\dump.sql

echo ✅ Database setup complete!
pause