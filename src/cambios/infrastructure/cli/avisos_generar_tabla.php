<?php

use src\cambios\application\AvisosGenerarTabla;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;

/**
 * Driver CLI para generar la tabla de avisos de cambios.
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
} else {
    $username = '';
    $esquema = '';
}
$document_root = isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT'])
    ? $_SERVER['DOCUMENT_ROOT']
    : '';
$dir_web = isset($_SERVER['DIRWEB']) && is_string($_SERVER['DIRWEB']) ? $_SERVER['DIRWEB'] : '';
$path = "$document_root/$dir_web";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("src/shared/global_header.inc");
require_once("src/shared/global_object.inc");

if ($username === '' || $esquema === '') {
    $username = ConfigGlobal::mi_usuario();
    $esquema = ConfigGlobal::mi_region_dl();
}

$useCase = DependencyResolver::get(AvisosGenerarTabla::class);
$resultado = $useCase->execute($username, $esquema);

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
