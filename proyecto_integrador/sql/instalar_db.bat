@echo off
setlocal EnableExtensions EnableDelayedExpansion

rem ============================================================
rem  Instalador de la base de datos - Proyecto Biblioteca
rem  Crea la base biblioteca_personal, el usuario biblioteca
rem  e importa las tablas y datos de sql\biblioteca.sql
rem ============================================================

set "SCRIPT_DIR=%~dp0"
set "SQL_DIR=%SCRIPT_DIR%"

rem --- Argumentos ---
rem  /y          -> no pedir confirmacion
rem  CONTRASENA  -> contrasena del root de MySQL (si tiene)
set "AUTO=0"
set "ROOT_PASS="
if /i "%~1"=="/y" (
  set "AUTO=1"
  shift
)
if not "%~1"=="" set "ROOT_PASS=%~1"

rem --- Puerto de MySQL que usa la aplicacion ---
if not defined MYSQL_PORT set "MYSQL_PORT=3306"

rem --- Buscar mysql.exe ---
set "MYSQL_EXE="
if defined MYSQL_BIN if exist "%MYSQL_BIN%\mysql.exe" set "MYSQL_EXE=%MYSQL_BIN%\mysql.exe"
if defined WAMP_MYSQL_BIN if exist "%WAMP_MYSQL_BIN%\mysql.exe" set "MYSQL_EXE=%WAMP_MYSQL_BIN%\mysql.exe"

if not defined MYSQL_EXE (
  for /f "delims=" %%D in ('dir /b /ad /o-n "C:\wamp\bin\mysql\mysql*" 2^>nul') do (
    if not defined MYSQL_EXE if exist "C:\wamp\bin\mysql\%%D\bin\mysql.exe" set "MYSQL_EXE=C:\wamp\bin\mysql\%%D\bin\mysql.exe"
  )
)
if not defined MYSQL_EXE (
  for /f "delims=" %%D in ('dir /b /ad /o-n "C:\wamp64\bin\mysql\mysql*" 2^>nul') do (
    if not defined MYSQL_EXE if exist "C:\wamp64\bin\mysql\%%D\bin\mysql.exe" set "MYSQL_EXE=C:\wamp64\bin\mysql\%%D\bin\mysql.exe"
  )
)
if not defined MYSQL_EXE if exist "C:\xampp\mysql\bin\mysql.exe" set "MYSQL_EXE=C:\xampp\mysql\bin\mysql.exe"

if not defined MYSQL_EXE (
  echo [ERROR] No se encontro mysql.exe.
  echo Define MYSQL_BIN con la carpeta bin de MySQL, por ejemplo:
  echo   set MYSQL_BIN=C:\wamp\bin\mysql\mysql9.7.4\bin
  exit /b 1
)

if not exist "%SQL_DIR%\crear_base_y_usuario.sql" (
  echo [ERROR] No existe "%SQL_DIR%\crear_base_y_usuario.sql"
  exit /b 1
)
if not exist "%SQL_DIR%\biblioteca.sql" (
  echo [ERROR] No existe "%SQL_DIR%\biblioteca.sql"
  exit /b 1
)

echo ============================================================
echo  Instalador de base de datos - Biblioteca
echo ============================================================
echo  MySQL: %MYSQL_EXE%
echo  Host:  127.0.0.1:%MYSQL_PORT%
echo  SQL:   %SQL_DIR%
echo.
echo  ADVERTENCIA:
echo  biblioteca.sql hace DROP TABLE y vuelve a crear las tablas.
echo  Se perderan los libros, usuarios e imagenes que ya existan.
echo.

if "%AUTO%"=="0" (
  set /p RESP=Continuar? Escribe S para si, N para no: 
  if /i not "!RESP!"=="S" (
    echo Cancelado.
    exit /b 0
  )
)

set "ROOT_ARGS=-u root --protocol=TCP --host=127.0.0.1 --port=%MYSQL_PORT%"
if defined ROOT_PASS set "ROOT_ARGS=%ROOT_ARGS% -p%ROOT_PASS%"

echo.
echo [1/3] Creando base de datos y usuario...
"%MYSQL_EXE%" %ROOT_ARGS% --default-character-set=utf8mb4 < "%SQL_DIR%\crear_base_y_usuario.sql"
if errorlevel 1 goto :error

echo [2/3] Importando tablas y datos...
"%MYSQL_EXE%" %ROOT_ARGS% --default-character-set=utf8mb4 biblioteca_personal < "%SQL_DIR%\biblioteca.sql"
if errorlevel 1 goto :error

echo [3/3] Verificando...
"%MYSQL_EXE%" %ROOT_ARGS% --default-character-set=utf8mb4 -e "USE biblioteca_personal; SHOW TABLES; SELECT COUNT(*) AS libros FROM libros;"
if errorlevel 1 goto :error

echo.
echo ============================================================
echo  Listo. Abre: http://localhost/biblioteca/
echo  Admin: admin@biblioteca.mx / Admin1234
echo ============================================================
exit /b 0

:error
echo.
echo [ERROR] La importacion fallo.
echo Revisa que Wamp este iniciado y que MySQL escuche en el puerto %MYSQL_PORT%.
echo Si el root tiene contrasena, ejecuta:
echo   instalar_db.bat TU_CONTRASENA
exit /b 1
