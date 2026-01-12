<?php

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

use core\ConfigGlobal;
use core\ViewTwig;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\entity\Ubi;
use web\Desplegable;
use web\Hash;

$obj = 'actividades\\\\model\\\\entity\\\\ActividadDl';
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = TRUE;
}

$oActividad = new ActividadAll($Qid_activ);
$a_status = StatusId::getArrayStatus();

$id_tipo_activ = $oActividad->getId_tipo_activ();
$dl_org = $oActividad->getDl_org();
$nom_activ = $oActividad->getNom_activ();
$id_ubi = $oActividad->getId_ubi();
//$desc_activ = $oActividad->['desc_activ'];
$f_ini = $oActividad->getF_ini()?->getFromLocal();
$h_ini = $oActividad->getH_ini();
$f_fin = $oActividad->getF_fin()?->getFromLocal();
$h_fin = $oActividad->getH_fin();
//$tipo_horario = $oActividad->['tipo_horario'];
$precio = $oActividad->getPrecio();
//$num_asistentes = $oActividad->['num_asistentes'];
$status = $oActividad->getStatus();
$observ = $oActividad->getObserv();
$nivel_stgr = $oActividad->getNivel_stgr();
//$observ_material = $oActividad->['observ_material'];
$lugar_esp = $oActividad->getLugar_esp();
$tarifa = $oActividad->getTarifa();
$id_repeticion = $oActividad->getId_repeticion();
$publicado = $oActividad->isPublicado();
$plazas = $oActividad->getPlazas();

// mirar permisos.
//if(ConfigGlobal::is_app_installed('procesos')) {
$_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
$oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

if ($oPermActiv->only_perm('ocupado')) {
    die();
}

if ($oPermActiv->have_perm_activ('ver') === TRUE) {
    $mod = "ver";
    if ($oPermActiv->have_perm_activ('modificar') === TRUE) {
        $mod = "editar";
    }
}

$oTipoActiv = new web\TiposActividades($id_tipo_activ);
$ssfsv = $oTipoActiv->getSfsvText();
$sasistentes = $oTipoActiv->getAsistentesText();
$sactividad = $oTipoActiv->getActividadText();
$snom_tipo = $oTipoActiv->getNom_tipoText();
$isfsv = $oTipoActiv->getSfsvId();


if (!empty($id_ubi) && $id_ubi != 1) {
    $oCasa = Ubi::newUbi($id_ubi);
    $nombre_ubi = $oCasa->getNombre_ubi();
    $delegacion = $oCasa->getDl();
    $region = $oCasa->getRegion();
    $sv = $oCasa->isSv();
    $sf = $oCasa->isSf();
} else {
    if ($id_ubi == 1 && $lugar_esp) $nombre_ubi = $lugar_esp;
    if (!$id_ubi && !$lugar_esp) $nombre_ubi = _("sin determinar");
}

$oDesplDelegacionesOrg = DelegacionDropdown::delegacionesURegiones(0, true, 'dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($dl_org);

$TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
$aOpciones = $TipoTarifaRepository->getArrayTipoTarifas($isfsv);
$oDesplPosiblesTipoTarifas = new Desplegable();
$oDesplPosiblesTipoTarifas->setOpciones($aOpciones);
$oDesplPosiblesTipoTarifas->setNombre('id_tarifa');
$oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);

$NivelStgrRepository = $GLOBALS['container']->get(NivelStgrRepositoryInterface::class);
$aOpciones = $NivelStgrRepository->getArrayNivelesStgr();
$oDesplNivelStgr = new Desplegable();
$oDesplNivelStgr->setOpciones($aOpciones);
$oDesplNivelStgr->setNombre('nivel_stgr');
$oDesplNivelStgr->setOpcion_sel($nivel_stgr);

$RepeticionRepository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
$aOpciones = $RepeticionRepository->getArrayRepeticion();
$oDesplRepeticion = new Desplegable();
$oDesplDelegacionesOrg->setOpciones($aOpciones);
$oDesplRepeticion->setNombre('id_repeticion');
$oDesplRepeticion->setOpcion_sel($id_repeticion);

$oHash = new Hash();
$camposForm = 'dl_org!f_fin!f_ini!h_fin!h_ini!extendida!iactividad_val!iasistentes_val!id_repeticion!id_ubi!inom_tipo_val!isfsv_val!lugar_esp!nivel_stgr!nom_activ!nombre_ubi!observ!plazas!precio!publicado!status!id_tarifa';
$camposNo = 'id_tipo_activ!mod';
$a_camposHidden = array(
    'id_tipo_activ' => $id_tipo_activ,
    'id_activ' => $Qid_activ,
    'ssfsv' => $ssfsv,
);
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!isfsv!ssfsv');
$h = $oHash1->linkSinVal();

$oActividadTipo = new actividades\model\ActividadTipo();
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);

$procesos_installed = ConfigGlobal::is_app_installed('procesos');

$status_txt = $a_status[$status];

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
    'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
    'plazas' => $plazas,
    'nombre_ubi' => $nombre_ubi,
    'id_ubi' => $id_ubi,
    'lugar_esp' => $lugar_esp,
    'oDesplPosiblesTipoTarifas' => $oDesplPosiblesTipoTarifas,
    'precio' => $precio,
    'observ' => $observ,
    'oDesplRepeticion' => $oDesplRepeticion,
    'oDesplNivelStgr' => $oDesplNivelStgr,
    'publicado' => $publicado,
    'oActividadTipo' => $oActividadTipo,
    'id_tipo_activ' => $id_tipo_activ,
    'web' => ConfigGlobal::getWeb(),
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewTwig('actividades/controller');
$oView->renderizar('calendario_form_actividad.html.twig', $a_campos);
