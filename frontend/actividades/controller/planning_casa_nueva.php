<?php

use frontend\actividades\helpers\ActividadesPermSupport;
use frontend\actividades\helpers\ActividadesMutacionSupport;
use frontend\actividades\helpers\ActividadesPayload;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Formulario para crear una actividad nueva desde el planning de casas.
 *
 * Desplegables desde `/src/actividades/actividad_ver_datos`; etiquetas de status
 * desde `actividad_status_labels_datos`; HTML del bloque tipo desde
 * `actividad_que_datos`. Sin `use src\...`.
 *
 * Migrado desde frontend/actividades/controller/planning_casa_nueva.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\actividades\helpers\ActividadStatusId;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
$Qid_ubi = \frontend\shared\helpers\PayloadCoercion::int(filter_input(INPUT_POST, 'id_ubi'));

$permiso_des = ActividadesPermSupport::permDes();

$id_tipo_activ = '';
$isfsv_input = OrbixRuntime::miSfsv();
$dl_org = OrbixRuntime::miDelef();
$status = ActividadStatusId::PROYECTO;

$data = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
    'id_activ' => 0,
    'isfsv' => $isfsv_input,
    'dl_org' => $dl_org,
    'id_ubi' => $Qid_ubi,
    'id_tipo_activ' => $id_tipo_activ,
]));

$render = ActividadesPayload::verRenderFromPayload($data);
$html_despl_dl_org = $render['html_despl_dl_org'];
$html_despl_tarifa = $render['html_despl_tarifa'];
$html_despl_nivel_stgr = $render['html_despl_nivel_stgr'];
$html_despl_idioma = $render['html_despl_idioma'];
$html_despl_repeticion = $render['html_despl_repeticion'];
$nombre_ubi = $render['nombre_ubi'];

$nom_activ = '';
$f_ini = '';
$h_ini = '';
$f_fin = '';
$h_fin = '';
$precio = '';
$observ = '';
$lugar_esp = '';
$publicado = false;
$plazas = '';

$ssfsv = '';
$sasistentes = '';
$sactividad = '';
$snom_tipo = '';

$urlMutacionAjax = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_mutacion_ajax.php';

$oHash = new HashFront();
$camposForm = ActividadesMutacionSupport::calendarioFormHashCamposForm();
$camposNo = 'id_tipo_activ!mod';
$a_camposHidden = [
    'id_tipo_activ' => $id_tipo_activ,
    'id_ubi' => $Qid_ubi,
    'ssfsv' => $ssfsv,
];
$oHash->setUrl($urlMutacionAjax);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);

$oHash1 = new HashFront();
$oHash1->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv!isfsv');
$h = $oHash1->linkSinValParams();

$labelsRow = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_status_labels_datos', [
    'with_all' => 'f',
]));
$a_status = ActividadesPayload::statusLabelsFromPayload($labelsRow);

$dataTipoBloque = PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
    'perm_jefe' => ActividadesPermSupport::permJefeTipoActiv() ? 't' : 'f',
    'id_tipo_activ' => $id_tipo_activ,
    'que' => '',
    'evitar_procesos' => 't',
    'sfsv' => $ssfsv,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'sactividad2' => '',
    'snom_tipo' => $snom_tipo,
    'extendida' => '',
]));
$actividad_tipo_html = PayloadCoercion::string($dataTipoBloque['actividad_tipo_html'] ?? '');

$procesos_installed = AppInstalled::is('procesos');

$status_txt = $a_status[PayloadCoercion::int($status)] ?? '';

$titulo = _("nueva actividad");
$accion = 'nuevo';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'obj' => $obj,
    'permiso_des' => $permiso_des,
    'accion' => $accion,
    'titulo' => $titulo,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'snom_tipo' => $snom_tipo,
    'ssfsv' => $ssfsv,
    'status' => $status,
    'status_txt' => $status_txt,
    'nom_activ' => $nom_activ,
    'f_ini' => $f_ini,
    'h_ini' => $h_ini,
    'f_fin' => $f_fin,
    'h_fin' => $h_fin,
    'plazas' => $plazas,
    'nombre_ubi' => $nombre_ubi,
    'id_ubi' => $Qid_ubi,
    'lugar_esp' => $lugar_esp,
    'precio' => $precio,
    'observ' => $observ,
    'publicado' => $publicado,
    'actividad_tipo_html' => $actividad_tipo_html,
    'id_tipo_activ' => $id_tipo_activ,
    'html_despl_dl_org' => $html_despl_dl_org,
    'html_despl_tarifa' => $html_despl_tarifa,
    'html_despl_nivel_stgr' => $html_despl_nivel_stgr,
    'html_despl_idioma' => $html_despl_idioma,
    'html_despl_repeticion' => $html_despl_repeticion,
    'web' => AppUrlConfig::getPublicAppBaseUrl(),
    'web_icons' => OrbixRuntime::getWebIcons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'calendario_hash_allow' => ActividadesMutacionSupport::calendarioMutacionSerializeAllowJson(),
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('calendario_form_actividad.html.twig', $a_campos);
