@echo off
set "zipname=arbeitsdienste.zip"
set "sourcepath=%CD%"
set "temppath=%TEMP%\backup_%date:~6,4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%"

:: Temporären Ordner erstellen
mkdir "%temppath%"

:: Dateien in den Temp-Ordner kopieren (ohne .git und sich selbst)
robocopy "%sourcepath%" "%temppath%" /E /XD .git /XF backup.bat

:: ZIP-Datei erstellen
powershell Compress-Archive -Path "%temppath%\*" -DestinationPath "%sourcepath%\%zipname%" -Force

:: Temporären Ordner löschen
rd /s /q "%temppath%"

echo ✅ Backup erfolgreich erstellt: %sourcepath%\%zipname%
pause
