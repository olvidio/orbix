<?php
// si lo ejecuto dese el crontab.
/* Hay que pasarle los argumentos que no tienen si se le llama por command line:
 $username;
 $password;
 $dir_web = orbix | pruebas;
 document_root = /home/dani/orbix_local
 $esquema_web = 'H-dlbv';
 $ubicacion = 'sv';
 $private => pongo el mismo valor que ubicación. Se supone que el cron está en private.
 $DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)
 */
if (!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $_SERVER['DIRWEB'] = $argv[3];
    $_SERVER['DOCUMENT_ROOT'] = $argv[4];
    putenv("UBICACION=$argv[5]");
    putenv("PRIVATE=$argv[5]");
    putenv("DB_SERVER=$argv[6]");
    putenv("ESQUEMA=$argv[7]");
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$dir_web = $_SERVER['DIRWEB'];
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use shared\infrastructure\consumirColaMail;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************
// FIN de  Cabecera global de URL de controlador ********************************

/* se ejecuta desde un cron (de momento) en el servidor exterior, que es el que tiene conexión al servidor de correo.
 Hay que hacerlo para todos los usuarios.
 Comprobar que tengan e-mail
 */

$oCosumidor = new consumirColaMail();
$oCosumidor->purge();
$oCosumidor->enviar();
