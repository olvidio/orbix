<?php

use src\cambios\application\AvisosGenerarTabla;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Driver CLI para generar la tabla de avisos de cambios.
 *
 * Invocado por cron, `Cambio::generarTabla()` (nohup) y el menú web
 * (`fnjs_link_submenu`). En petición HTTP devuelve JSON ContestarJson;
 * en CLI mantiene la salida HTML/texto legacy.
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

$isWeb = PHP_SAPI !== 'cli';

if ($username === '' || $esquema === '') {
    $username = ConfigGlobal::mi_usuario();
    $esquema = ConfigGlobal::mi_region_dl();
}

/**
 * @param array{err_fila: string, bucle_infinito: bool} $resultado
 */
function avisos_generar_tabla_html_errores(array $resultado): string
{
    $err_tabla = _("error al apuntar cambio usuario en");
    $err_tabla .= ' ' . ConfigGlobal::$web_server . '-->' . date('c') . '<br>';
    $err_tabla .= '<table><tr>';
    $err_tabla .= '<th>' . _("schema") . '</th>';
    $err_tabla .= '<th>' . _("id_item_cmb") . '</th>';
    $err_tabla .= '<th>' . _("id_activ / id_usuario") . '</th>';
    $err_tabla .= '<th>' . _("motivo / aviso tipo") . '</th>';
    $err_tabla .= '</tr>';
    $err_tabla .= $resultado['err_fila'];
    $err_tabla .= '</table>';

    return $err_tabla;
}

try {
    $useCase = DependencyResolver::get(AvisosGenerarTabla::class);
    $resultado = $useCase->execute($username, $esquema);
} catch (\RuntimeException $e) {
    if ($isWeb) {
        ContestarJson::enviar($e->getMessage(), '');
        return;
    }
    throw $e;
}

if ($isWeb) {
    if ($resultado['bucle_infinito']) {
        ContestarJson::enviar(_('Algo falla'), '');
        return;
    }

    if ($resultado['err_fila'] !== '') {
        $html = '<p class="alert">' . _("Se han detectado incidencias al generar la tabla de avisos") . '</p>';
        $html .= avisos_generar_tabla_html_errores($resultado);
        ContestarJson::enviar('', ['html' => $html]);
        return;
    }

    $html = '<p>' . _("Tabla de avisos generada.") . '</p>';
    $html .= '<p class="comentario">' . _("Puede consultar los avisos desde el menú «ver avisos».") . '</p>';
    ContestarJson::enviar('', ['html' => $html]);
    return;
}

if ($resultado['err_fila'] !== '') {
    echo avisos_generar_tabla_html_errores($resultado);
}

if ($resultado['bucle_infinito']) {
    fwrite(STDERR, _("Algo falla") . "\n");
    exit(1);
}
