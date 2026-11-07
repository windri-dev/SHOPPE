@echo off
setlocal

if "%~1"=="" goto help
if "%~1"=="help" goto help

:: Forward command ke GitHub CLI
gh issue %*
goto end

:help
echo ===================================================
echo     GitHub Issues CLI Helper (SHOPPE Repository)
echo ===================================================
echo.
echo Penggunaan:
echo   issue list                        - Lihat semua issue yang terbuka
echo   issue list --state all            - Lihat semua issue (termasuk yang sudah closed)
echo   issue create                      - Buat issue baru secara interaktif
echo   issue create --title "..." --body "..." - Buat issue langsung dari terminal
echo   issue view ^<nomor^>                - Lihat detail isi issue di terminal
echo   issue view ^<nomor^> --web          - Buka issue langsung di browser
echo   issue comment ^<nomor^> --body "..." - Tambahkan komentar pada issue
echo   issue close ^<nomor^>               - Tutup / selesaikan issue
echo   issue reopen ^<nomor^>              - Buka kembali issue yang ditutup
echo.
echo Contoh:
echo   issue create --title "Fix: checkout layout" --body "Perbaiki padding form checkout"
echo   issue view 1
echo   issue close 1
echo.
:end
endlocal
