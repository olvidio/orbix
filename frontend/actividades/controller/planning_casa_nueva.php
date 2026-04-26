<?php
/**
 * Formulario para crear una actividad nueva desde el planning de casas.
 *
 * Los desplegables se montan en
 * `src\actividades\application\ActividadVerDatos` (llamado con id_activ=0)
 * y se consumen via PostRequest, de modo que este controlador no toca
 * repositorios ni entidades.
 *
 * Migrado desde frontend/actividades/controller/planning_casa_nueva.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use src\actividades\application\ActividadTipo;
use src\actividades\domain\value_objects\StatusId;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$permiso_des = false;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = true;
}

$id_tipo_activ = '';
$isfsv_input = OrbixRuntime::miSfsv();
$dl_org = OrbixRuntime::miDelef();
$status = StatusId::PROYECTO;

$data = PostRequest::getDataFromUrl('/src/actividades/actividad_ver_datos', [
    'id_activ' => 0,
    'isfsv' => $isfsv_input,
    'dl_org' => $dl_org,
    'id_ubi' => $Qid_ubi,
    'id_tipo_activ' => $id_tipo_activ,
]);

$isfsv = (int)($data['isfsv'] ?? $isfsv_input);
$html_despl_dl_org = (string)($data['html_despl_dl_org'] ?? '');
$html_despl_tarifa = (string)($data['html_despl_tarifa'] ?? '');
$html_despl_nivel_stgr = (string)($data['html_despl_nivel_stgr'] ?? '');
$html_despl_idioma = (string)($data['html_despl_idioma'] ?? '');
$html_despl_repeticion = (string)($data['html_despl_repeticion'] ?? '');
$nombre_ubi = (string)($data['nombre_ubi'] ?? '');

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

$oHash = new Hash();
$camposForm = 'dl_org!f_fin!f_ini!h_fin!h_ini!extendida!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!id_tarifa';
$camposNo = 'id_tipo_activ!mod';
$a_camposHidden = [
    'id_tipo_activ' => $id_tipo_activ,
    'id_ubi' => $Qid_ubi,
    'ssfsv' => $ssfsv,
];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);

$oHash1 = new Hash();
$oHash1->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv!isfsv');
$h = $oHash1->linkSinValParams();

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$procesos_installed = AppInstalled::is('procesos');

$a_status = StatusId::getArrayStatus();
$status_txt = $a_status[$status] ?? '';

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
    'oActividadTipo' => $oActividadTipo,
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

$oView = new ViewNewTwig('actividades/controller');
$oView->renderizar('calendario_form_actividad.html.twig', $a_campos);
