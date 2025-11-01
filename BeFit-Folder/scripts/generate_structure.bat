@echo off
:: ========================================================
:: generate_structure.bat - Directory Structure Generator
:: 
:: Purpose: This script generates a complete recursive listing
:: of all files and folders in the project directory
:: and saves it to filestructure.txt.
::
:: Usage:
::   1. Place this file in your project's scripts folder
::   2. Double-click to run it
::   3. Output will be saved to ../filestructure.txt (one level up)
::
:: Notes:
::   - Uses relative paths so it works on any computer
::   - Uses 'dir' command with /s (recursive) and /b (bare format)
::   - Overwrites existing filestructure.txt without warning
::   - For appending instead of overwriting, change ">" to ">>"
:: ========================================================

:: Get the parent directory path (one level up from scripts folder)
set "parent_dir=%~dp0.."

:: Generate directory listing and save to filestructure.txt
dir "%parent_dir%" /s /b > "%parent_dir%\filestructure.txt"

echo Directory structure successfully saved to:
echo %parent_dir%\filestructure.txt
echo.
pause