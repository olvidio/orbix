<?php
/**
 * Controlador frontend de la pantalla "seleccionar lugar para una actividad".
 *
 * Presenta 5 opciones de busqueda (historial, region, nombre, lugar especial,
 * por determinar). Se abre como ventana auxiliar que devuelve el id_ubi al
 * `window.opener` (formulario de la actividad).
 *
 * Los desplegables dinamicos (casas frequentes, regiones) se cargan via AJAX
 * desde el endpoint backend /src/actividades/actividad_select_ubi_desplegable.
 */

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$isfsv = empty($_REQUEST['isfsv']) ? '' : $_REQUEST['isfsv'];
$ssfsv = empty($_REQUEST['ssfsv']) ? '' : $_REQUEST['ssfsv'];

if (empty($isfsv)) {
    if ($ssfsv === 'sv') {
        $isfsv = 1;
    }
    if ($ssfsv === 'sf') {
        $isfsv = 2;
    }
}
$isfsv = (int)$isfsv;

$dl_org = (string)($_REQUEST['dl_org'] ?? '');

// URL + hash para cargar desplegables (freq/region) via AJAX.
$url_desplegable = rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividades/actividad_select_ubi_desplegable';
$oHashDespl = new Hash();
$oHashDespl->setUrl($url_desplegable);
$oHashDespl->setCamposForm('tipo!dl_org!isfsv');
$h_desplegable = $oHashDespl->linkSinValParams();

$oHash = new Hash();
$oHash->setUrl(rtrim(ConfigGlobal::getWeb(), '/') . '/src/actividades/actividad_tipo_get');
$oHash->setCamposForm('extendida!modo!salida!entrada!isfsv');
$h = $oHash->linkSinValParams();

$oHash1 = new Hash();
$oHash1->setCamposForm('id_ubi_1');

$oHash2 = new Hash();
$oHash2->setCamposForm('filtro_lugar!lst_lugar');

$oHash3 = new Hash();
$oHash3->setCamposForm('nombre_ubi');
$oHash3->setArraycamposHidden([
    'tipo' => 'tot',
    'loc' => 'tot',
]);

$oHash4 = new Hash();
$oHash4->setCamposForm('frm_4_nombre_ubi');

$txt_alert = _("no olvides ajustar el nombre de la actividad");

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'oHash1' => $oHash1,
    'oHash2' => $oHash2,
    'oHash3' => $oHash3,
    'oHash4' => $oHash4,
    'isfsv' => $isfsv,
    'dl_org' => $dl_org,
    'url_desplegable' => $url_desplegable,
    'h_desplegable' => $h_desplegable,
    'txt_alert' => $txt_alert,
];

$oView = new ViewNewPhtml('frontend\\actividades\\controller');
$oView->renderizar('actividad_select_ubi.phtml', $a_campos);
