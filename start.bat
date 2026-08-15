@echo off
setlocal
cd /d "%~dp0"

if not exist ".tools\php\php.exe" (
    where php >nul 2>nul
    if errorlevel 1 (
        echo PHP nao encontrado no computador.
        echo Execute setup-php.bat uma vez para baixar o PHP, depois rode start.bat novamente.
        pause
        exit /b 1
    )
)

echo Starting MktScheduler...

:: Iniciar Backend PHP em uma nova janela
echo Starting PHP Backend on http://localhost:18000...
start "Backend PHP" cmd /k call "%~dp0backend\start-server.bat"

:: Iniciar Frontend React 19 (Vite)
echo Starting React Frontend on http://localhost:4200...
cd /d "%~dp0frontend"
call npm start
