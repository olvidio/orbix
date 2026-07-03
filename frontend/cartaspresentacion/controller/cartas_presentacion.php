<?php
/**
 * Pantalla principal del modulo `cartaspresentacion` — shell con filtro
 * dl/r + poblacion, listado AJAX de centros y modal de modificacion.
 */
use frontend\cartaspresentacion\helpers\CartaspresentacionPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\DesplegableArray;
use frontend\cartaspresentacion\helpers\CartasPresentacionShellRender;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$shell = CartaspresentacionPayload::postData(PostRequest::getDataFromUrl('/src/cartaspresentacion/cartas_presentacion_shell_data', []));
$shell = CartasPresentacionShellRender::enrich($shell);
$view = CartaspresentacionPayload::shellViewFromPayload($shell);
$mi_dele = $view['mi_dele'];

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
    'url_ctr' => $view['url_ctr'],
    'h_ctr' => $view['h_ctr'],
    'url_lista' => $view['url_lista'],
    'hash_lista_html' => $view['hash_lista_html'],
    'url_form' => $view['url_form'],
    'h_form' => $view['h_form'],
    'url_poblaciones' => $view['url_poblaciones'],
    'h_poblaciones' => $view['h_poblaciones'],
    'url_update' => $view['url_update'],
    'url_eliminar' => $view['url_eliminar'],
    'h_eliminar' => $view['h_eliminar'],
    'txt_confirmar_eliminar' => (string)_("¿Está seguro que quiere quitar los datos de presentación de este centro?"),
];

$oView = new ViewNewPhtml('frontend\\cartaspresentacion\\view');
$oView->renderizar('cartas_presentacion.phtml', $a_campos);
