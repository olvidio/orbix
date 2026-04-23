<?php
/**
 * Formulario para modificar una actividad desde el planning de casas.
 *
 * La carga de la actividad y la construccion de los desplegables se hace en
 * `src\actividades\application\ActividadVerDatos` via PostRequest, de modo
 * que este controlador no toca repositorios ni entidades.
 *
 * Migrado desde frontend/actividades/controller/planning_casa_modificar.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use src\actividades\application\ActividadTipo;
use src\actividades\domain\value_objects\StatusId;
use web\Hash;
use web\TiposActividades;

require_once("frontend/shared/global_header_front.inc");

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

$oTipoActiv = new TiposActividades($id_tipo_activ);
$ssfsv = $oTipoActiv->getSfsvText();
$sasistentes = $oTipoActiv->getAsistentesText();
$sactividad = $oTipoActiv->getActividadText();
$snom_tipo = $oTipoActiv->getNom_tipoText();

$oHash = new Hash();
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

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/frontend/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!isfsv!ssfsv');
$h = $oHash1->linkSinValParams();

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$procesos_installed = ConfigGlobal::is_app_installed('procesos');

$a_status = StatusId::getArrayStatus();
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
    'oActividadTipo' => $oActividadTipo,
    'id_tipo_activ' => $id_tipo_activ,
    'html_despl_dl_org' => $html_despl_dl_org,
    'html_despl_tarifa' => $html_despl_tarifa,
    'html_despl_nivel_stgr' => $html_despl_nivel_stgr,
    'html_despl_idioma' => $html_despl_idioma,
    'html_despl_repeticion' => $html_despl_repeticion,
    'web' => ConfigGlobal::getWeb(),
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewNewTwig('actividades/controller');
$oView->renderizar('calendario_form_actividad.html.twig', $a_campos);
