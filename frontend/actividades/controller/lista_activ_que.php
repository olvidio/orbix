<?php
/**
 * Pantalla de filtros para listados particulares de sr/sg. La accion real
 * (`listar`) se delega al controlador frontend lista_activ.php, que a su
 * vez consume el endpoint backend /src/actividades/lista_activ_datos.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividades_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qque = (string)filter_input(INPUT_POST, 'que');

$permiso_des = actividades_perm_des();

$url_lista = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/lista_activ.php';

$oHash = new HashFront();
$oHash->setCamposForm('seccion!status!empiezamin!empiezamax!asist!c_activ!tit_list_grupo');
$oHash->setArraycamposHidden([
    'que' => $Qque,
]);

$chk_sr_sf = '';
$chk_sr_sv = '';
$titulo = '';
$sr_sg = '';
switch ($Qque) {
    case 'list_activ_sr_sf':
        $titulo = _("datos del listado actividades san rafael sf");
        $sr_sg = 'sr';
        $chk_sr_sf = 'checked';
        break;
    case 'list_activ_sr':
        $titulo = _("datos del listado actividades san rafael");
        $sr_sg = 'sr';
        $chk_sr_sv = 'checked';
        break;
    case 'list_activ_inv_sg':
        $titulo = _("datos del listado actividades san gabriel");
        $sr_sg = 'sg';
        break;
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'permiso_des' => $permiso_des,
    'titulo' => $titulo,
    'sr_sg' => $sr_sg,
    'chk_sr_sf' => $chk_sr_sf,
    'chk_sr_sv' => $chk_sr_sv,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'url_lista' => $url_lista,
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('lista_activ_que.html.twig', $a_campos);
