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
 
 >>> Se crea la tabla también en el servidor interno, para cuando se rehace la DB por fallos de sincronización.
 */

// CREATE TABLE bucardo_test ( id_item SERIAL, time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, status TEXT);
$oDbl = $GLOBALS['oDBPC'];

// Borar las filas para tener sólo 30.
$sql = "DELETE FROM bucardo_test
WHERE id_item NOT IN (
    SELECT id_item
    FROM (
        SELECT id_item
        FROM bucardo_test
        ORDER BY id_item DESC
        LIMIT 30
        ) AS foo
    );
";
if (($oDblSt = $oDbl->query($sql)) === FALSE) {
    return FALSE;
}

// mirar la última fila de status
if (($oDblSt = $oDbl->query("SELECT * FROM bucardo_test WHERE tipo='status' ORDER BY time DESC LIMIT 1")) === FALSE) {
    return FALSE;
}
$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);


$time_db = $aDades['time'];
$status_txt = $aDades['status'];
// ej: 2021-05-11 11:29:14.293397
$oUltimaSync = new DateTime($time_db);
$oHoy = new DateTime();

$interval = $oUltimaSync->diff($oHoy);
$minutos = $interval->format("%i");

$error_txt = '';
if ($minutos > 30) {
    $error_txt .= "Ultima sicronización: " . $oUltimaSync->format("Y-m-d h:i:s");
}

$pos1 = strpos($status_txt, 'Bad');
if ($pos1 !== false) {
    $error_txt .= "Se encontró 'Bad' en 'status_txt' en la posición $pos1";
    $error_txt .= "<br>";
    $error_txt .= makear($status_txt);
}

// mirar la última fila de restart
if (($oDblSt = $oDbl->query("SELECT * FROM bucardo_test WHERE tipo='restart' ORDER BY time DESC LIMIT 1")) === FALSE) {
    return FALSE;
}
$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);


$time_db = $aDades['time'];
$restart_txt = $aDades['status'];

$pos1 = strpos($restart_txt, 'fail');
if ($pos1 !== false) {
    $error_txt .= "Se encontró 'fail' en 'restart.log' ";
    $error_txt .= "<br>";
    $error_txt .= makear($status_txt);
}


$email = "dserrabou@gmail.com, salvagual@gmail.com";

if (!empty($error_txt)) enviar_mail($email, $error_txt);

function enviar_mail($email, $error_txt)
{

    $asunto = _("Syncronización bucardo");
    $cuerpo = '
	<html>
	<head>
	<title>Burado status</title>
	</head>
	<body>';
    $cuerpo .= $error_txt;
    $cuerpo .= '</body></html>';

    //Envío en formato HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";

    //Dirección del remitente
    $headers .= "From: Bucardo <no-Reply@moneders.net>\r\n";
    //Dirección de respuesta
    $headers .= "Reply-To: no-Reply@moneders.net\r\n";
    //Ruta del mensaje desde origen a destino
    $headers .= "Return-path: no-Reply@moneders.net\r\n";


    //echo "($email<br>$asunto<br>$cuerpo<br>$headers)<br>";
    mail($email, $asunto, $cuerpo, $headers);
}

function makear($status_txt)
{
    $html = str_replace('\s*<td>\|<\/td>\s*', '', $status_txt);
    $html2 = preg_replace('<tr>\s*<td>[\=\+]*<\/td>\s*<\/tr>', '', $html);

    return $html2;
}