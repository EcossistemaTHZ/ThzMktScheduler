@echo off
setlocal
cd /d "%~dp0"

set "PHP_DIR=%~dp0.tools\php"
set "PHP_ZIP=%~dp0.tools\php.zip"
set "PHP_TMP=%~dp0.tools\_php_tmp"
set "PHP_URL=https://windows.php.net/downloads/releases/latest/php-8.4-nts-Win32-vs17-x64-latest.zip"

if exist "%PHP_DIR%\php.exe" (
    echo PHP ja instalado em .tools\php
    exit /b 0
)

echo Baixando PHP 8.4 (Windows x64)...
if not exist "%~dp0.tools" mkdir "%~dp0.tools"
curl.exe -L --fail -o "%PHP_ZIP%" "%PHP_URL%"
if errorlevel 1 (
    echo Falha ao baixar o PHP. Verifique sua conexao com a internet.
    exit /b 1
)

echo Extraindo...
if exist "%PHP_TMP%" rd /s /q "%PHP_TMP%"
mkdir "%PHP_TMP%" >nul
tar.exe -xf "%PHP_ZIP%" -C "%PHP_TMP%"
if errorlevel 1 (
    echo Falha ao extrair o PHP.
    exit /b 1
)
del "%PHP_ZIP%"

set "EXTRACTED_DIR="
if exist "%PHP_TMP%\php.exe" set "EXTRACTED_DIR=%PHP_TMP%"
if not defined EXTRACTED_DIR (
    for /d %%D in ("%PHP_TMP%\*") do if exist "%%D\php.exe" set "EXTRACTED_DIR=%%D"
)
if not defined EXTRACTED_DIR (
    echo Falha ao localizar o PHP extraido.
    exit /b 1
)

if exist "%PHP_DIR%" rd /s /q "%PHP_DIR%"
move "%EXTRACTED_DIR%" "%PHP_DIR%" >nul
if exist "%PHP_TMP%" rd /s /q "%PHP_TMP%"

set "BASE=%~dp0"
set "BASE=%BASE:\=/%"
> "%PHP_DIR%\php.ini" (
    echo extension_dir=%BASE%.tools/php/ext
    echo extension=pdo_sqlite
    echo extension=sqlite3
    echo extension=mbstring
    echo extension=openssl
    echo extension=curl
    echo extension=fileinfo
)

echo.
echo PHP instalado em .tools\php
"%PHP_DIR%\php.exe" -v
echo.
echo Pronto! Execute start.bat para iniciar o projeto.
echo Obs: se aparecer erro sobre VCRUNTIME140.dll, instale o
echo "Microsoft Visual C++ Redistributable (x64)" e rode de novo.
exit /b 0
