@echo off
REM 🎯 Arbeitsdienste Plugin - Einfacher Development Helper

if "%1"=="" goto help
if "%1"=="version" goto version
if "%1"=="set" goto set_version
if "%1"=="release" goto release

echo ❌ Unbekannter Befehl: %1
goto help

:help
echo.
echo 🎯 Arbeitsdienste Plugin - Development Helper
echo.
echo Verfügbare Befehle:
echo   dev.bat version        - Zeigt aktuelle Version
echo   dev.bat set 2.2        - Setzt Version auf 2.2
echo   dev.bat release 2.2    - Setzt Version UND erstellt GitHub Release
echo.
goto end

:version
echo 📋 Aktuelle Version:
php includes/version.php
goto end

:set_version
if "%2"=="" (
    echo ❌ Version fehlt!
    echo 💡 Verwendung: dev.bat set 2.2
    goto end
)
php update-version.php %2
goto end

:release
if "%2"=="" (
    echo ❌ Version fehlt!
    echo 💡 Verwendung: dev.bat release 2.2
    goto end
)
echo 🚀 Erstelle Release für Version %2...
php update-version.php %2 --release
goto end

:end
