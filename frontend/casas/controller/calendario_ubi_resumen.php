<?php
/**
 * Pantalla `calendario_ubi_resumen`: estudio económico y de ocupación
 * de una casa.
 *
 * Muestra el formulario de filtros (casa + incremento de tarifas +
 * incremento de gastos). El usuario pulsa "resumen sv" o "resumen sf"
 * y el cuerpo se carga via AJAX desde
 * `frontend/casas/controller/calendario_ubi_resumen_body.php`, que a
 * su vez pide los datos JSON a `/src/casas/calendario_ubi_resumen_data`
 * y renderiza el HTML.
 *
 * Migrada desde `apps/casas/controller/calendario_ubi_resumen.php` +
 * `calendario_ubi_resumen_ajax.php` siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$QG = (int)filter_input(INPUT_POST, 'G');
$Qinc_t = (int)filter_input(INPUT_POST, 'inc_t');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$filtro = ['active' => '1'];
if (!($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
    $miSfsv = OrbixRuntime::miSfsv();
    if ($miSfsv === 1) {
        $filtro['sv'] = '1';
    } elseif ($miSfsv === 2) {
        $filtro['sf'] = '1';
    }
}
$dataCasas = PostRequest::getDataFromUrl('/src/ubis/casas_opciones_data', $filtro);
$aCasas = (is_array($dataCasas) && isset($dataCasas['opciones']) && is_array($dataCasas['opciones']))
    ? $dataCasas['opciones'] : [];
$oDesplCasas = new Desplegable();
$oDesplCasas->setNombre('id_ubi');
$oDesplCasas->setOpciones($aCasas);
$oDesplCasas->setOpcion_sel($Qid_ubi);

$web = AppUrlConfig::getPublicAppBaseUrl();

$oHashBody = new Hash();
$oHashBody->setUrl($web . '/frontend/casas/controller/calendario_ubi_resumen_body.php');
$oHashBody->setCamposForm('id_ubi!G!inc_t!seccion');
$url_body = $web . '/frontend/casas/controller/calendario_ubi_resumen_body.php' . $oHashBody->linkSinVal();

$oHashTarifas = new Hash();
$oHashTarifas->setUrl($web . '/src/actividadtarifas/tarifa_ubi_update_inc');
$oHashTarifas->setCamposForm('id_ubi!year!inc_cantidad');
$url_tarifas = $web . '/src/actividadtarifas/tarifa_ubi_update_inc' . $oHashTarifas->linkSinVal();

$oHashForm = new Hash();
$oHashForm->setCamposForm('id_ubi!G!inc_t!seccion');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHashForm,
    'oDesplCasas' => $oDesplCasas,
    'QG' => $QG,
    'Qinc_t' => $Qinc_t,
    'url_body' => $url_body,
    'url_tarifas' => $url_tarifas,
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('calendario_ubi_resumen.phtml', $a_campos);
