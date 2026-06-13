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
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/casas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$QG = (int)filter_input(INPUT_POST, 'G');
$Qinc_t = (int)filter_input(INPUT_POST, 'inc_t');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$filtro = ['active' => '1'];
if (!actividades_have_perm_oficina('des') && !actividades_have_perm_oficina('vcsd')) {
    $miSfsv = OrbixRuntime::miSfsv();
    if ($miSfsv === 1) {
        $filtro['sv'] = '1';
    } elseif ($miSfsv === 2) {
        $filtro['sf'] = '1';
    }
}
$dataCasas = casas_post_data(PostRequest::getDataFromUrl('/src/ubis/casas_opciones_data', $filtro));
$aCasas = casas_calendario_casas_opciones($dataCasas)['opciones'];
$oDesplCasas = new Desplegable();
$oDesplCasas->setNombre('id_ubi');
$oDesplCasas->setOpciones($aCasas);
$oDesplCasas->setOpcion_sel(casas_desplegable_opcion_sel($Qid_ubi));

$web = AppUrlConfig::getPublicAppBaseUrl();

$oHashBody = new HashFront();
$oHashBody->setUrl($web . '/frontend/casas/controller/calendario_ubi_resumen_body.php');
$oHashBody->setCamposForm('id_ubi!G!inc_t!seccion');
$url_body = $web . '/frontend/casas/controller/calendario_ubi_resumen_body.php' . $oHashBody->linkSinVal();

$oHashTarifas = new HashFront();
$oHashTarifas->setUrl($web . '/src/actividadtarifas/tarifa_ubi_update_inc');
$oHashTarifas->setCamposForm('id_ubi!year!inc_cantidad');
$url_tarifas = $web . '/src/actividadtarifas/tarifa_ubi_update_inc' . $oHashTarifas->linkSinVal();

$oHashForm = new HashFront();
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
