@echo off
setlocal enabledelayedexpansion

:: Database connection settings
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_NAME=befit_db
set DB_USER=root
set DB_PASS=

:: Title
title BeFit DB User Deletion Tool
color 0A

:start
cls
echo #############################################
echo #    BeFit Database - Complete User Deletion    #
echo #############################################
echo.

:: Get user ID to delete
set USER_ID=
set /p USER_ID=Enter the User ID you want to delete (or type 'exit' to quit): 

:: Check if user wants to exit
if /i "%USER_ID%"=="exit" goto :eof

:: Improved validation - check if input is numeric
set NUMERIC=true
for /f "delims=0123456789" %%a in ("%USER_ID%") do set NUMERIC=false

if "%NUMERIC%"=="false" (
    echo.
    echo ERROR: Please enter a valid numeric User ID.
    pause
    goto start
)

:: Create temporary SQL file to get user info
set INFO_FILE=%temp%\user_info_%random%.sql
set OUTPUT_FILE=%temp%\user_info_%random%.txt

(
echo SELECT name, email, created_at FROM users WHERE id = %USER_ID%;
) > "%INFO_FILE%"

:: Get user information
mysql -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% < "%INFO_FILE%" > "%OUTPUT_FILE%"

:: Display user information
echo.
echo USER INFORMATION:
echo -----------------
type "%OUTPUT_FILE%"
echo -----------------

:: Check if user exists
for /f "tokens=*" %%a in ('type "%OUTPUT_FILE%" ^| find /c /v ""') do set LINES=%%a
if %LINES% LSS 2 (
    echo.
    echo ERROR: User ID %USER_ID% not found in database.
    del "%INFO_FILE%" >nul 2>&1
    del "%OUTPUT_FILE%" >nul 2>&1
    pause
    goto start
)

:: Confirm deletion
echo.
echo WARNING: This will PERMANENTLY delete all data for the above user
echo          including orders, workout history, and recommendations.
echo.
set CONFIRM=
set /p CONFIRM=Are you sure you want to proceed? (y/n): 

if /i "%CONFIRM%" neq "y" (
    echo.
    echo Operation cancelled.
    del "%INFO_FILE%" >nul 2>&1
    del "%OUTPUT_FILE%" >nul 2>&1
    pause
    goto start
)

:: Create temporary SQL file for deletion
set SQL_FILE=%temp%\delete_user_%random%.sql

(
echo START TRANSACTION;
echo;
echo -- Delete password resets
echo DELETE FROM password_resets WHERE user_id = %USER_ID%;
echo;
echo -- Delete order items
echo DELETE oi FROM order_items oi
echo JOIN orders o ON oi.order_id = o.id
echo WHERE o.user_id = %USER_ID%;
echo;
echo -- Delete orders
echo DELETE FROM orders WHERE user_id = %USER_ID%;
echo;
echo -- Delete recommended supplements
echo DELETE FROM recommended_supplements WHERE user_id = %USER_ID%;
echo;
echo -- Delete workout history
echo DELETE FROM user_workout_history WHERE user_id = %USER_ID%;
echo;
echo -- Delete workout plans
echo DELETE FROM workout_plans WHERE user_id = %USER_ID%;
echo;
echo -- Finally, delete the user
echo DELETE FROM users WHERE id = %USER_ID%;
echo;
echo COMMIT;
) > "%SQL_FILE%"

:: Execute the SQL
echo.
echo Deleting user %USER_ID% and all related data...
mysql -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% < "%SQL_FILE%"

:: Check if successful
if errorlevel 1 (
    echo.
    echo ERROR: Failed to delete user %USER_ID%
    echo Check your database connection settings and try again.
) else (
    echo.
    echo SUCCESS: User %USER_ID% and all related data have been deleted.
)

:: Clean up
del "%INFO_FILE%" >nul 2>&1
del "%OUTPUT_FILE%" >nul 2>&1
del "%SQL_FILE%" >nul 2>&1

:: Ask to continue
echo.
pause
goto start