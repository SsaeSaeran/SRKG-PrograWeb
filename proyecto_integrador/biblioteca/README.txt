
Requisitos

-  PHP 8.x
-  MySQL 8.x
-  Apache (WampServer, XAMPP o similar)
-  Navegador 

No se necesita Composer ni ninguna libreria de PHP. Bootstrap se carga desde CDN.

- - - 

Crear la base de datos y el usuario

Abrir MySQL  como root y ejecutar
sql/crear_base_y_usuario.sql, que contiene exactamente esto:

sql
CREATE DATABASE IF NOT EXISTS biblioteca_personal
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

- -  Se crea el usuario para las dos formas de entrar: localhost y 127.0.0.1
CREATE USER IF NOT EXISTS 'biblioteca'@'localhost'
    IDENTIFIED BY 'Biblioteca#2026';

CREATE USER IF NOT EXISTS 'biblioteca'@'127.0.0.1'
    IDENTIFIED BY 'Biblioteca#2026';

GRANT ALL PRIVILEGES ON biblioteca_personal.* TO 'biblioteca'@'localhost';
GRANT ALL PRIVILEGES ON biblioteca_personal.* TO 'biblioteca'@'127.0.0.1';
FLUSH PRIVILEGES;


Asi la aplicacion nunca se conecta como root, sino con el usuario biblioteca, que solo
tiene permisos sobre esta base de datos.


Crear las tablas y los datos de prueba

Ejecutar el archivo `sql/biblioteca.sql` (contiene las tablas, los roles, las cuentas y
104 libros de ejemplo).

Desde la consola de MySQL:

bash
mysql - u biblioteca - p biblioteca_personal < sql/biblioteca.sql


O abrir el archivo en Workbench y ejecutarlo completo.

Copiar el proyecto

Copiar la carpeta `biblioteca` dentro de `C:\wamp64\www\`.
La aplicación queda en: <http://localhost/biblioteca/>

Revise la conexion

En `config/conexion.php` deben coincidir los datos:

php
$host    = '127.0.0.1';
$puerto  = '3306';
$dbname  = 'biblioteca_personal';
$usuario = 'biblioteca';
$clave   = 'Biblioteca#2026';

La conexion se guarda en la variable `$pdo`

- - - 

Credenciales de ejemplo

Administrador 
admin@biblioteca.mx
Admin1234

Usuario 
lector@biblioteca.mx
Lector1234

Sae
sae@gmail.com
Saeran1234

Video de prueba
video@gmail.com
video1234

- - - 

Estructura


index.php                  Entrada: redirige según la sesión
login.php                  Formulario de inicio de sesión
procesar_login.php         Valida correo y contraseña
registro.php               Formulario de registro público
procesar_registro.php      Valida y crea la cuenta
logout.php                 Cierra la sesión
dashboard.php              Indicadores y gráfica
imagen.php                 Sirve la portada (bytes desde la base)
config
   - conexion.php           Conexión PDO ($pdo)
   - auth.php               Sesión, roles y funciones de apoyo
   - imagenes.php           Valida y lee la portada (guarda en la base)
includes
   - header.php             Barra de navegación
   - footer.php             Pie de página
libros
   - index.php              Listado, buscador y miniaturas
   - ver.php                Detalle del libro (?id=) + descripción y portada
   - crear.php              Alta de libros (con portada)
   - editar.php             Edición completa de libros
   - eliminar.php           Baja lógica (solo admin)
assets
   - css/estilos.css        Estilos propios (Flexbox y position)
   - js/grafica.js          Gráfica de barras con Canvas
sql/
    - crear_base_y_usuario.sql   Crea la base y el usuario (solo root)
    - biblioteca.sql             Tablas y datos iniciales
    - instalar_db.bat            Intalador de la base de datos


- - - 

Funcionalidades

-  Registro publico con validación y correo unico.
-  Contraseñas guardadas con `password_hash()` y verificadas con `password_verify()`.
-  Login, logout y protección de todas las paginas internas.
-  Dos roles guardados en la tabla `roles`:
    -  Administrador: ve y edita todos los libros, y puede deshabilitarlos.
    -  Usuario: solo ve y edita sus propios libros; no puede eliminarlos.
-  CRUD de libros (crear, listar, editar, eliminar de forma logica).
-  Portada por libro: se sube una imagen desde los archivos de la PC
    La imagen se guarda dentro de MySQL (columna `LONGBLOB`), junto con su tipo MIME.
    Para mostrarla, el navegador pide `imagen.php?id=X`.
-  Descripción por libro: texto largo (hasta 2000 caracteres).
-  Pagina de libros/ver.php: una plantilla para los libros. Tiene a la izquierda la portada 
    con su formulario para subir o quitar la imagen; a la derecha los datos del libro, la descripción y su editor.
-  Miniaturas en el listado; si un libro no tiene portada se muestra la inicial del titulo.
-  Buscador por titulo o autor con `LIKE`.
-  Panel con total, pendientes y terminados segun el rol.-
-  Grafica de barras por estado (extra puntos profeee deme puntos o lloro TnT).

- - - 

Notas

-  El eliminado es logico: el registro queda con `activo = 0` y deja de mostrarse
-  La grafica está hecha con Canvas y JavaScript 
-  Las portadas se guardan en la base de datos (LONGBLOB). Si vuelves a ejecutar el sql,
las tablas se recrean y las portadas se pierden, no se como hacer que se mantengan
- - - 

Ayuda de la IA por un error al exportar la base de datos:
Corrección aplicada el 2026- 11- 19 y instalación en otras computadoras
La base `biblioteca_personal` y el usuario `biblioteca` ya existían, pero la base
no tenía tablas. Por eso al intentar iniciar sesion salía:


SQLSTATE[42S02]: Base table or view not found:
1146 Table 'biblioteca_personal.usuarios' doesn't exist


Lo que pasó es que la conexión a MySQL si funcionaba; lo que faltaba era ejecutar el segundo script,
el que crea las tablas y carga los datos.

PARA INSTALAR EN OTRA COMPUTADORA

Copiar en una memoria la carpeta biblioteca completa. Si la otra computadora ya tiene libros y portadas capturados, llevar además un respaldo de la
base de datos 

Copiar la carpeta biblioteca dentro de C:\wamp64\www\, de modo que quede:

C:\wamp64\www\biblioteca\login.php

CREAR LA BASE DE DATOS

La forma más rápida es ejecutar el instalador:

Abrir C:\wamp64\www\biblioteca\sql\.
Doble clic en instalar_db.bat.
Escribir S para confirmar.

El instalador crea la base biblioteca_personal, el usuario biblioteca con la contraseña
Biblioteca#2026, y carga las tablas y los 104 libros de ejemplo.

Si el root de MySQL tiene contraseña, abrir una consola en esa carpeta y ejecutar:

instalar_db.bat TU_CONTRASENA_DE_ROOT

Si MySQL no está en el puerto 3306 (por ejemplo 3307), primero:

set MYSQL_PORT=3307
instalar_db.bat

También se puede hacer a mano desde phpMyAdmin, importando primero
sql/crear_base_y_usuario.sql y después sql/biblioteca.sql.

El archivo crear_base_y_usuario.sql incluye ALTER USER para que, si el usuario biblioteca ya
existía con otra contraseña, quede con la que espera la aplicación.

REVISAR LA CONEXIÓN

Abrir config/conexion.php y confirmar que coincidan el puerto y la contraseña:

$host    = '127.0.0.1';
$puerto  = '3306';
$dbname  = 'biblioteca_personal';
$usuario = 'biblioteca';
$clave   = 'Biblioteca#2026';

Si en esa computadora MySQL usa otro puerto, cambiar $puerto. Si se cambió la contraseña del
usuario biblioteca, cambiar $clave en los dos lados.

LLEVAR LOS DATOS REALES (LIBROS Y PORTADAS)

biblioteca.sql trae datos de ejemplo y borra lo que hubiera. Si ya se capturaron libros de
verdad, hay que respaldar la base en la computadora vieja y restaurarla en la nueva.

En la computadora vieja, abrir una consola y ejecutar (pide la contraseña de root):

"C:\wamp64\bin\mysql\mysql8.4.7\bin\mysqldump.exe" - u root - p - - default- character- set=utf8mb4 - - single- transaction biblioteca_personal > biblioteca_respaldo.sql

La carpeta mysql8.4.7 puede tener otro número en la computadora nueva: dentro de
C:\wamp64\bin\mysql\ se usa la que exista. El archivo biblioteca_respaldo.sql incluye las
tablas, los usuarios y las portadas (que están dentro de la base).

En la computadora nueva:

Crear la base y el usuario ejecutando sql/crear_base_y_usuario.sql como root. No hace falta
importar biblioteca.sql: el respaldo ya trae las tablas y los datos.
Restaurar el respaldo:

"C:\wamp64\bin\mysql\mysql8.4.7\bin\mysql.exe" - u root - p - - default- character- set=utf8mb4 biblioteca_personal < biblioteca_respaldo.sql

Así se conservan los ids, las contraseñas ya cifradas y las portadas.

PROBLEMAS COMUNES

El icono de Wamp está naranja. Casi siempre es el puerto 80 ocupado (IIS, Skype u otro Apache).
Se cambia el puerto de Apache desde el menú de Wamp (Apache, httpd.conf, buscar Listen 80 y
ServerName localhost:80) y se entra con http://localhost:8080/biblioteca/.

MySQL no arranca. Puede ser el puerto 3306 ocupado por otro MySQL. Wamp permite cambiar el
puerto desde su menú; después hay que ajustar $puerto en config/conexion.php y ejecutar el
instalador con set MYSQL_PORT=...

Aparece "Access denied for user 'biblioteca'". El usuario existe con otra contraseña. Ejecutar
de nuevo sql/crear_base_y_usuario.sql como root: incluye ALTER USER para dejarla en
Biblioteca#2026.

No se ven los estilos ni el icono. Wamp tiene que estar encendido y la carpeta debe estar en
www. Hacer una recarga forzada con Ctrl + F5, porque el navegador guarda el CSS viejo.

No deja subir portadas. Revisar en php.ini que upload_max_filesize y post_max_size sean al
menos 4M (el proyecto acepta imágenes de hasta 2 MB).