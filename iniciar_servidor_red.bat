@echo off
setlocal EnableExtensions
chcp 65001 >nul
title HelpDesk TI - Servidor en red

rem ==========================================================================
rem  Inicia HelpDesk TI para que otros equipos se conecten (Tailscale o red local).
rem
rem  clic derecho  y"ejecutar como administrador",
rem  para crear la regla del Firewall de Windows (se hace una sola vez).
rem
rem  El puerto debe coincidir con el de app.baseURL en .env (http://localhost:8080/).
rem ==========================================================================

set "PUERTO=8080"
set "REGLA=HelpDesk TI - puerto %PUERTO%"

cd /d "%~dp0"

echo.
echo  ==========================================================
echo    HelpDesk TI - Servidor para red local / Tailscale para entorno de pruebas
echo  ==========================================================
echo.

rem --- 1. PHP ---------------------------------------------------------------
where php >nul 2>&1
if not errorlevel 1 goto :php_ok
if exist "C:\xampp\php\php.exe" (
    set "PATH=C:\xampp\php;%PATH%"
    goto :php_ok
)
echo  [ERROR] No se encontró PHP. Instala XAMPP o agrega php.exe al PATH.
goto :fin_error
:php_ok

rem --- 2. Puerto libre ------------------------------------------------------
powershell -NoProfile -Command "if (Get-NetTCPConnection -LocalPort %PUERTO% -State Listen -ErrorAction SilentlyContinue) { exit 1 }"
if not errorlevel 1 goto :puerto_ok
echo  [ERROR] El puerto %PUERTO% ya está en uso. ¿Hay otro "php spark serve" abierto?
echo          Ciérralo e intenta de nuevo.
goto :fin_error
:puerto_ok

rem --- 3. MySQL (puerto leído desde .env) -----------------------------------
powershell -NoProfile -Command "$p = 3306; $m = Select-String -Path '.env' -Pattern '^\s*database\.default\.port\s*=\s*(\d+)' -ErrorAction SilentlyContinue | Select-Object -First 1; if ($m) { $p = [int]$m.Matches[0].Groups[1].Value }; $c = New-Object Net.Sockets.TcpClient; try { if (-not $c.ConnectAsync('127.0.0.1', $p).Wait(1500)) { exit 1 } } catch { exit 1 } finally { $c.Close() }"
if errorlevel 1 (
    echo  [AVISO] MySQL no responde. Inicia MySQL antes de usar el sistema.
    echo.
)

rem --- 4. Firewall de Windows -----------------------------------------------
rem  Solo acepta conexiones desde Tailscale (100.64.0.0/10) y desde la red local.
netsh advfirewall firewall show rule name="%REGLA%" >nul 2>&1
if not errorlevel 1 goto :firewall_ok
net session >nul 2>&1
if errorlevel 1 (
    echo  [AVISO] Falta la regla del Firewall para el puerto %PUERTO%.
    echo          Cierra esta ventana y ejecuta este archivo con clic derecho ^>
    echo          "Ejecutar como administrador" ^(solo la primera vez^).
    echo.
    goto :firewall_ok
)
netsh advfirewall firewall add rule name="%REGLA%" dir=in action=allow protocol=TCP localport=%PUERTO% remoteip=100.64.0.0/10,LocalSubnet profile=any >nul
if errorlevel 1 (
    echo  [AVISO] No se pudo crear la regla del Firewall.
) else (
    echo  [OK] Regla del Firewall creada: "%REGLA%".
)
echo.
:firewall_ok

rem --- 5. Direcciones para conectarse ---------------------------------------
set "TAILSCALE=tailscale"
where tailscale >nul 2>&1
if errorlevel 1 set "TAILSCALE=%ProgramFiles%\Tailscale\tailscale.exe"
set "IP_TAILSCALE="
for /f "delims=" %%i in ('call "%TAILSCALE%" ip -4 2^>nul') do if not defined IP_TAILSCALE set "IP_TAILSCALE=%%i"

echo  Desde ESTE equipo:
echo     http://localhost:%PUERTO%
echo.
echo  Desde el equipo de tu compañero, por Tailscale:
if defined IP_TAILSCALE (
    echo     http://%IP_TAILSCALE%:%PUERTO%
) else (
    echo     [Tailscale no está instalado o no está conectado]
)
echo.
echo  Desde la misma red local (laboratorio / mismo Wi-Fi):
powershell -NoProfile -Command "Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue | Where-Object { $_.IPAddress -match '^(10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)' -and $_.InterfaceAlias -notmatch 'vEthernet|VirtualBox|VMware|Hamachi|ZeroTier|Loopback' } | ForEach-Object { '    http://' + $_.IPAddress + ':%PUERTO%   (' + $_.InterfaceAlias + ')' }"
echo.
echo  ----------------------------------------------------------
echo   Deja esta ventana abierta mientras usen el sistema.
echo   Para detener el servidor: Ctrl + C
echo  ----------------------------------------------------------
echo.

rem --- 6. Servidor ----------------------------------------------------------
php spark serve --host 0.0.0.0 --port %PUERTO%
goto :fin

:fin_error
echo.
pause
exit /b 1

:fin
pause
