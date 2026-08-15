@echo off
setlocal
cd /d "%~dp0"

set "PHP=%~dp0..\.tools\php\php.exe"
if not exist "%PHP%" (
    where php >nul 2>nul
    if errorlevel 1 (
        echo PHP nao encontrado no computador.
        echo Execute setup-php.bat uma vez para baixar o PHP, e tente novamente.
        pause
        exit /b 1
    )
    set "PHP=php"
)

echo Backend PHP rodando em http://localhost:18000
"%PHP%" -S localhost:18000 -t public
