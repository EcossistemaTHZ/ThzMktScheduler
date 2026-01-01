@echo off
echo Starting MktScheduler...

:: Iniciar Backend PHP em uma nova janela
echo Starting PHP Backend on http://localhost:8000...
start "Backend PHP" cmd /k "cd backend && php -S localhost:8000 -t public"

:: Iniciar Frontend Angular na mesma janela (ou nova se preferir)
echo Starting Angular Frontend on http://localhost:4200...
cd frontend && npm start
