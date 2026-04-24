<?php

/**
 * Driver CLI para consumir la cola de mails pendientes.
 *
 * Se ejecuta desde crontab en el servidor exterior (el unico con
 * acceso al MTA). La logica vive en
 * `src\shared\application\EnviarMailsEnCola`. Este fichero:
 *   1. Parsea `$argv` para reconstruir la sesion.
 *   2. Arranca el contenedor (ver requires: salida de CLI, sin public/index).
 *   3. Llama al use case.
 *   4. Imprime un resumen en stdout (util en los logs de cron).
 *
 * Parametros posicionales:
 *   argv[1] $username
 *   argv[2] $password
 *   argv[3] $dirweb
 *   argv[4] $document_root
 *   argv[5] $ubicacion   (se usa tambien como $private)
 *   argv[6] $DB_SERVER   (1 interno / 2 exterior)
 *   argv[7] $esquema_web
 *
 * Migrado desde `apps/shared/controller/enviar_mails_en_cola.php`.
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

use src\shared\application\EnviarMailsEnCola;

// No entra por public/index.php: hace falta el mismo arranque que el front (autoload, sesión, contenedor).
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");

$resumen = (new EnviarMailsEnCola())->execute();

if (PHP_SAPI === 'cli') {
    fwrite(
        STDOUT,
        sprintf(
            "[%s] enviar_mails_en_cola: enviados=%d purgados=%s\n",
            date('c'),
            $resumen['enviados'],
            $resumen['purgados'] ? 'si' : 'no'
        )
    );
}
