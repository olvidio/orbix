<?php

use frontend\actividades\helpers\ActividadesPermSupport;
use frontend\actividades\helpers\ActividadesPostInput;
use frontend\actividades\helpers\ActividadesMutacionSupport;
use frontend\actividades\helpers\ActividadesPayload;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Formulario para modificar una actividad desde el planning de casas.
 *
 * La carga de la actividad y los desplegables viene de
 * `/src/actividades/actividad_ver_datos`; textos del tipo y HTML del bloque tipo
 * desde ese payload y `/src/actividades/actividad_que_datos`; etiquetas de status
 * desde `/src/actividades/actividad_status_labels_datos`. Sin `use src\...`.
 *
 * Migrado desde frontend/actividades/controller/planning_casa_modificar.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\actividades\helpers\PrefillPermActividadesFases;
use frontend\shared\FrontBootstrap;
use src\permisos\domain\PermisosActividades;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
$Qid_activ = ActividadesPostInput::idActivFromPost();
if ($Qid_activ === 0) {
    $Qid_activ = PayloadCoercion::int(filter_input(INPUT_POST, 'id_activ'));
}

$permiso_des = ActividadesPermSupport::permDes();

$data = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
    'id_activ' => $Qid_activ,
]);

$entidad = ActividadesPayload::entidadFromVerDatos($data);
$render = ActividadesPayload::verRenderFromPayload($data);

$id_tipo_activ = $entidad['id_tipo_activ'];
$dl_org = $entidad['dl_org'];
$nom_activ = $entidad['nom_activ'];
$id_ubi = $entidad['id_ubi'];
$f_ini = $entidad['f_ini'];
$h_ini = $entidad['h_ini'];
$f_fin = $entidad['f_fin'];
$h_fin = $entidad['h_fin'];
$precio = $entidad['precio'];
$status = $entidad['status'];
$observ = $entidad['observ'];
$lugar_esp = $entidad['lugar_esp'];
$publicado = $entidad['publicado'];
$plazas = $entidad['plazas'];

$html_despl_dl_org = $render['html_despl_dl_org'];
$html_despl_tarifa = $render['html_despl_tarifa'];
$html_despl_nivel_stgr = $render['html_despl_nivel_stgr'];
$html_despl_idioma = $render['html_despl_idioma'];
$html_despl_repeticion = $render['html_despl_repeticion'];
$nombre_ubi = $render['nombre_ubi'];
$ssfsv = $render['ssfsv'];
$sasistentes = $render['sasistentes'];
$sactividad = $render['sactividad'];
$snom_tipo = $render['snom_tipo'];

$oPermActividades = ActividadesPermSupport::oPermActividades();
if (!$oPermActividades instanceof PermisosActividades) {
    die();
}
$oPermActividades->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
PrefillPermActividadesFases::desdeBackend($Qid_activ);
$oPermActiv = $oPermActividades->getPermisoActual('datos');

if ($oPermActiv->only_perm('ocupado')) {
    die();
}

$mod = '';
if ($oPermActiv->have_perm_activ('ver') === true) {
    $mod = 'ver';
    if ($oPermActiv->have_perm_activ('modificar') === true) {
        $mod = 'editar';
    }
}

$labelsRow = PostRequest::getDataFromUrl('/src/actividades/actividad_status_labels_datos', [
    'with_all' => 'f',
]);
$a_status = ActividadesPayload::statusLabelsFromPayload($labelsRow);

$dataTipoBloque = PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
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
]);
$actividad_tipo_html = PayloadCoercion::string($dataTipoBloque['actividad_tipo_html'] ?? '');

$urlMutacionAjax = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_mutacion_ajax.php';

$oHash = new HashFront();
$camposForm = ActividadesMutacionSupport::calendarioFormHashCamposForm();
$camposNo = 'id_tipo_activ!mod';
$a_camposHidden = [
    'id_tipo_activ' => $id_tipo_activ,
    'id_activ' => $Qid_activ,
    'ssfsv' => $ssfsv,
];
$oHash->setUrl($urlMutacionAjax);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);

$oHash1 = new HashFront();
$oHash1->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!isfsv!ssfsv');
$h = $oHash1->linkSinValParams();

$procesos_installed = AppInstalled::is('procesos');

$status_txt = $a_status[$status] ?? '';

$titulo = _("modificar actividad");
$accion = 'editar';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'obj' => $obj,
    'oPermActiv' => $oPermActiv,
    'mod' => $mod,
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
    'id_ubi' => $id_ubi,
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
