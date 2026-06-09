<?php
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

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
$Qid_activ = (int)filter_input(INPUT_POST, 'id_activ');

$permiso_des = false;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = true;
}

$data = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
    'id_activ' => $Qid_activ,
]);

$entidad = (array)($data['entidad'] ?? []);
$isfsv = (int)($data['isfsv'] ?? 0);
$html_despl_dl_org = (string)($data['html_despl_dl_org'] ?? '');
$html_despl_tarifa = (string)($data['html_despl_tarifa'] ?? '');
$html_despl_nivel_stgr = (string)($data['html_despl_nivel_stgr'] ?? '');
$html_despl_idioma = (string)($data['html_despl_idioma'] ?? '');
$html_despl_repeticion = (string)($data['html_despl_repeticion'] ?? '');
$nombre_ubi = (string)($data['nombre_ubi'] ?? '');

$id_tipo_activ = (string)($entidad['id_tipo_activ'] ?? '');
$dl_org = (string)($entidad['dl_org'] ?? '');
$nom_activ = (string)($entidad['nom_activ'] ?? '');
$id_ubi = (int)($entidad['id_ubi'] ?? 0);
$f_ini = (string)($entidad['f_ini'] ?? '');
$h_ini = (string)($entidad['h_ini'] ?? '');
$f_fin = (string)($entidad['f_fin'] ?? '');
$h_fin = (string)($entidad['h_fin'] ?? '');
$precio = $entidad['precio'] ?? '';
$status = (int)($entidad['status'] ?? 0);
$observ = (string)($entidad['observ'] ?? '');
$lugar_esp = (string)($entidad['lugar_esp'] ?? '');
$publicado = (bool)($entidad['publicado'] ?? false);
$plazas = $entidad['plazas'] ?? '';

$_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
PrefillPermActividadesFases::desdeBackend($Qid_activ);
$oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

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

$ssfsv = (string)($data['ssfsv'] ?? '');
$sasistentes = (string)($data['sasistentes'] ?? '');
$sactividad = (string)($data['sactividad'] ?? '');
$snom_tipo = (string)($data['snom_tipo'] ?? '');

$labelsRow = PostRequest::getDataFromUrl('/src/actividades/actividad_status_labels_datos', [
    'with_all' => 'f',
]);
$a_status = $labelsRow['id_to_label'] ?? [];

$dataTipoBloque = PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
    'perm_jefe' => 'f',
    'id_tipo_activ' => $id_tipo_activ,
    'que' => '',
    'sfsv' => $ssfsv,
    'sasistentes' => $sasistentes,
    'sactividad' => $sactividad,
    'sactividad2' => '',
    'snom_tipo' => $snom_tipo,
    'extendida' => '',
]);
$actividad_tipo_html = (string)($dataTipoBloque['actividad_tipo_html'] ?? '');

$oHash = new HashFront();
$camposForm = 'dl_org!f_fin!f_ini!h_fin!h_ini!extendida!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!id_tarifa';
$camposNo = 'id_tipo_activ!mod';
$a_camposHidden = [
    'id_tipo_activ' => $id_tipo_activ,
    'id_activ' => $Qid_activ,
    'ssfsv' => $ssfsv,
];
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
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('calendario_form_actividad.html.twig', $a_campos);
