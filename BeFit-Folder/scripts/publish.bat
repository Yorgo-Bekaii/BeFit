@echo off
set "MYSQL_USER=root"
set "MYSQL_PASSWORD="  # Add password here if needed
set "DB_NAME=befit_db"
set "DUMP_PATH=..\database\dump.sql"

:: Skip column statistics to prevent false errors
set "DUMP_OPTIONS=--skip-column-statistics"

echo Exporting %DB_NAME%...
if defined MYSQL_PASSWORD (
    mysqldump %DUMP_OPTIONS% -u %MYSQL_USER% -p%MYSQL_PASSWORD% %DB_NAME% > "%DUMP_PATH%"
) else (
    mysqldump %DUMP_OPTIONS% -u %MYSQL_USER% %DB_NAME% > "%DUMP_PATH%"
)

if %errorlevel% equ 0 (
    echo ✅ Success! Database exported to:
    echo %CD%\%DUMP_PATH%
) else (
    echo ❌ Real export failed! Check:
    echo 1. MySQL credentials
    echo 2. Database existence
    echo 3. File paths
)
pause