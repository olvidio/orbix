<?php

/**
 * Driver CLI para generar la tabla de avisos de cambios.
 *
 * Invocado:
 *   - Desde crontab (ver apps/cambios/README.md).
 *   - Desde `Cambio::generarTabla()` via `exec(nohup php … &)`.
 *   - Desde el menu web (con `$argv` vacio) — en ese caso se reutilizan los
 *     valores de sesion vigentes.
 *
 * La logica de negocio vive en `src\cambios\application\AvisosGenerarTabla`.
 * Este fichero solo:
 *   1. Parsea `$argv` a `$_SERVER`/`putenv`/`$_POST` para el bootstrap legacy.
 *   2. Arranca el contenedor (`global_header.inc` + `global_object.inc`).
 *   3. Llama al use case.
 *   4. Imprime la tabla HTML de errores en stdout (legacy) y sale con codigo
 *      `1` si detecta bucle infinito.
 *
 * Parametros posicionales (cuando se invoca por CLI):
 *   argv[1] $username
 *   argv[2] $password
 *   argv[3] $dirweb       (ej. "orbix" | "pruebas")
 *   argv[4] $document_root (ej. "/home/dani/orbix_local")
 *   argv[5] $ubicacion    (ej. "sv" | "sf")
 *   argv[6] $esquema_web  (ej. "H-dlbv")
 *   argv[7] $private      (ej. "sf" para servidor exterior; "x" si no aplica)
 *   argv[8] $DB_SERVER    (1 interno / 2 exterior)
 *
 * OJO php.ini CLI:
 *     include_path = ".:/usr/share/php:/home/dani/orbix_local/orbix"
 */

if (!empty($argv[1])) {
    $_POST['username'] = $argv[1];
    $_POST['password'] = $argv[2];
    $_SERVER['DIRWEB'] = $argv[3];
    $_SERVER['DOCUMENT_ROOT'] = $argv[4];
    putenv("UBICACION=$argv[5]");
    putenv("ESQUEMA=$argv[6]");
    putenv("PRIVATE=$argv[7]");
    putenv("DB_SERVER=$argv[8]");

    $username = $argv[1];
    $esquema = $argv[6];
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$dir_web = $_SERVER['DIRWEB'];
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use core\ConfigGlobal;
use src\cambios\application\AvisosGenerarTabla;

require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");

if (empty($argv[1])) { // Si lo hago desde el menu
    $username = ConfigGlobal::mi_usuario();
    $esquema = ConfigGlobal::mi_region_dl();
}

$resultado = (new AvisosGenerarTabla())->execute($username, $esquema);

if (!empty($resultado['err_fila'])) {
    $err_tabla = _("error al apuntar cambio usuario en");
    $err_tabla .= " " . ConfigGlobal::$web_server . '-->' . date('c') . ": " . _("Ya existe") . "<br>";
    $err_tabla .= '<table><tr>';
    $err_tabla .= '<th>' . _("schema") . '</th>';
    $err_tabla .= '<th>' . _("id_item_cmb") . '</th>';
    $err_tabla .= '<th>' . _("id_usuario") . '</th>';
    $err_tabla .= '<th>' . _("aviso tipo") . '</th>';
    $err_tabla .= '</tr>';
    $err_tabla .= $resultado['err_fila'];
    $err_tabla .= '</table>';

    echo $err_tabla;
}

if ($resultado['bucle_infinito']) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, _("Algo falla") . "\n");
        exit(1);
    }
    echo _("Algo falla");
}
