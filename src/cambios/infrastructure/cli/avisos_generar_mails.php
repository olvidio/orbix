<?php

use src\cambios\application\AvisosEncolarMails;
use src\shared\infrastructure\DependencyResolver;

/**
 * Driver CLI para encolar los avisos pendientes de cada usuario.
 *
 * Debe ejecutarse por crontab en el servidor **interior** (SV y SF),
 * donde hay acceso a las tablas de personas para resolver nombres.
 * Inserta en `cola_mails`; el envío lo hace `enviar_mails_en_cola.php`
 * en el servidor exterior (DMZ).
 *
 * No debe correrse en la DMZ: allí no hay nombres de personas no-sacd.
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
$document_root = isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT'])
    ? $_SERVER['DOCUMENT_ROOT']
    : '';
$dir_web = isset($_SERVER['DIRWEB']) && is_string($_SERVER['DIRWEB']) ? $_SERVER['DIRWEB'] : '';
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("src/shared/global_header.inc");
require_once("src/shared/global_object.inc");

$useCase = DependencyResolver::get(AvisosEncolarMails::class);
$resumen = $useCase->execute();

if (PHP_SAPI === 'cli') {
    fwrite(
        STDOUT,
        sprintf(
            "[%s] avisos_generar_mails: encolados=%d sin_email=%d avisos_totales=%d\n",
            date('c'),
            $resumen['encolados'],
            $resumen['usuarios_sin_email'],
            $resumen['total_avisos']
        )
    );
}
