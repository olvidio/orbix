<?php
/**
 * Pantalla principal del modulo `cartaspresentacion` — shell con filtro
 * dl/r + poblacion, listado AJAX de centros y modal de modificacion.
 */
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\DesplegableArray;
use frontend\cartaspresentacion\helpers\CartasPresentacionShellRender;
require_once 'frontend/shared/global_header_front.inc';

$shell = PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_shell_data', []);
if (!is_array($shell)) {
    $shell = [];
}
$shell = CartasPresentacionShellRender::enrich($shell);
$mi_dele = (string)($shell['mi_dele'] ?? '');

$aOpcionesCiudad = [
    'get_dl' => $mi_dele,
    'get_r' => _("regiones"),
];
$oSelCiudades = new DesplegableArray('', $aOpcionesCiudad, '');
$oSelCiudades->setBlanco('t');
$oSelCiudades->setNombre('tipo_lista');
$oSelCiudades->setAction('fnjs_poblacion()');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oSelCiudades' => $oSelCiudades,
    'url_ctr' => (string)($shell['url_ctr'] ?? ''),
    'h_ctr' => (string)($shell['h_ctr'] ?? ''),
    'url_lista' => (string)($shell['url_lista'] ?? ''),
    'hash_lista_html' => (string)($shell['hash_lista_html'] ?? ''),
    'url_form' => (string)($shell['url_form'] ?? ''),
    'h_form' => (string)($shell['h_form'] ?? ''),
    'url_poblaciones' => (string)($shell['url_poblaciones'] ?? ''),
    'h_poblaciones' => (string)($shell['h_poblaciones'] ?? ''),
    'url_update' => (string)($shell['url_update'] ?? ''),
    'url_eliminar' => (string)($shell['url_eliminar'] ?? ''),
    'h_eliminar' => (string)($shell['h_eliminar'] ?? ''),
    'txt_confirmar_eliminar' => (string)_("¿Está seguro que quiere quitar los datos de presentación de este centro?"),
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\view');
$oView->renderizar('cartas_presentacion.phtml', $a_campos);
