<?php
/**
 * Muestra un formulario para poder seleccionar un rgupo de actividades
 *
 */

use actividades\model\ActividadTipo;
use core\ConfigGlobal;
use core\ViewTwig;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\entity\Ubi;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************

//Necesario cuando tengo que buscar el desplegable dl_org según permisos en procesos
// (Como también afecta al status de la actividad, mejor rehacer toda la página).
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_activ = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');
}

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$obj = 'actividades\\model\\entity\\ActividadAll';

$aQuery = array('pau' => 'a',
    'id_pau' => $Qid_activ,
    'obj_pau' => $Qobj_pau);
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$godossiers = Hash::link('apps/dossiers/controller/dossiers_ver.php?' . http_build_query($aQuery));

$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = TRUE;
}

$alt = '';
$dos = '';
if (!empty($Qid_activ)) { // caso de modificar
    $alt = _("ver dossiers");
    $dos = _("dossiers");
    // caso particular de cambiar el tipo
    if ($Qmod !== 'cambiar_tipo') {
        $Qmod = 'editar';
    }

    $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
    $oActividad = $ActividadAllRepository->findById($Qid_activ);
    $a_status = StatusId::getArrayStatus();

    $id_tipo_activ = $oActividad->getId_tipo_activ();
    $dl_org = $oActividad->getDl_org();
    $nom_activ = $oActividad->getNom_activ();
    $id_ubi = $oActividad->getId_ubi();
    //$desc_activ = $oActividad->['desc_activ'];
    $f_ini = $oActividad->getF_ini()->getFromLocal();
    $h_ini = $oActividad->getH_ini()?->format('H:i');
    $f_fin = $oActividad->getF_fin()->getFromLocal();
    $h_fin = $oActividad->getH_fin()?->format('H:i');
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
    $_SESSION['oPermActividades']->setActividad($Qid_activ, $id_tipo_activ, $dl_org);
    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

    if ($oPermActiv->only_perm('ocupado')) {
        die();
    }

    $oTipoActiv = new TiposActividades($id_tipo_activ);
    $ssfsv = $oTipoActiv->getSfsvText();
    $sasistentes = $oTipoActiv->getAsistentesText();
    $sactividad = $oTipoActiv->getActividadText();
    $snom_tipo = $oTipoActiv->getNom_tipoText();
    $isfsv = $oTipoActiv->getSfsvId();

    // Para incluir o no la dl 
    $Bdl = 't';
    if (ConfigGlobal::is_app_installed('procesos')) {
        if ($oPermActiv->have_perm_activ('ver')) {
            $Bdl = 't';
        } else {
            $Bdl = 'f';
        }
    }

} else { // caso de nueva actividad
    $Qmod = 'nuevo';
    $isfsv = ConfigGlobal::mi_sfsv();

    $a_status = StatusId::getArrayStatus();
    // Valores por defecto
    $dl_org = ConfigGlobal::mi_delef();
    // si es nueva, obligatorio estado: proyecto (14.X.2011)
    $status = 1;
    $id_ubi = 0;
    $lugar_esp = '';
    $tarifa = '';
    $nivel_stgr = 'r';
    $id_repeticion = 0;
    $id_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $id_tipo_activ = urldecode($id_tipo_activ); // En el caso de sr, sg, se pasa la cadena tipo 2[789]... (con [, que se encodan).
    $id_activ = '';

    // si ya le digo un tipo de actividad, sobre-escribo el parametro isfsv:
    if (!empty($id_tipo_activ)) {
        $isfsv = (integer)substr($id_tipo_activ, 0, 1);
        // También prodria buscar la id_tarifa:
        $aWhereT = [];
        $aWhereT['id_tipo_activ'] = $id_tipo_activ;
        $aWhereT['_ordre'] = 'id_serie';
        $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $cActiTipoTarifa = $RelacionTarifaTipoActividadRepository->getTipoActivTarifas($aWhereT);
        if (!empty($cActiTipoTarifa) && $cActiTipoTarifa > 0) {
            $tarifa = $cActiTipoTarifa[0]->getId_tarifa();
        }
    }
    if (is_true($permiso_des)) {
        if (empty($id_tipo_activ)) {
            //valor por defecto. Si está vacio dira que no tiene permiso.
            $id_tipo_activ = '1';
            $ssfsv = 'sv';
        }
        // En el caso de des puedo crear acrividades de sf.
        if ($isfsv == 1) {
            $ssfsv = 'sv';
            $dl_org = ConfigGlobal::mi_dele();
        }
        if ($isfsv == 2) {
            $ssfsv = 'sf';
            $dl_org = ConfigGlobal::mi_dele() . 'f';
        }
    } else {
        if ($isfsv == 1) {
            $ssfsv = 'sv';
        }
        if ($isfsv == 2) {
            $ssfsv = 'sf';
        }
    }


    $sasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $sactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $snom_tipo = '';

    $nom_activ = '';
    $f_ini = '';
    $h_ini = '';
    $f_fin = '';
    $h_fin = '';
    $plazas = '';
    $precio = '';
    $observ = '';
    $publicado = '';

    // Para incluir o no la dl
    $Bdl = 't';
    if (ConfigGlobal::is_app_installed('procesos')) {
        // Depende del proceso, para dl u otra
        // primera fase de los posibles procesos.
        // si no permiso para ninguno de los dos => die
        // si para dl, incluir la dl org
        // si dl_ex, idem.
        $_SESSION['oPermActividades']->setId_tipo_activ($id_tipo_activ);

        $crearPropia = $_SESSION['oPermActividades']->getPermisoCrear(TRUE);
        if ($crearPropia === FALSE) {
            echo '<br>';
            die (_("No tiene permiso para crear una actividad de este tipo"));
        } else {
            $of_responsable_txt = $crearPropia['of_responsable_txt'];
            if (!empty($of_responsable_txt) && $_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
                $Bdl = 't';
                $status = $crearPropia['status'];
            } else {
                if (empty($of_responsable_txt)) {
                    $Bdl = 't';
                    $status = $crearPropia['status'];
                } else {
                    $Bdl = 'f';
                    $crearEx = $_SESSION['oPermActividades']->getPermisoCrear(FALSE);
                    $of_responsable_txt = $crearEx['of_responsable_txt'];
                    $status = $crearEx['status'];
                    if (!$_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
                        die (_("No tiene permiso para crear una actividad de este tipo"));
                    }
                }
            }
        }

    }
    // Para el permiso del botón guardar, en el caso de editar. Cuando es nuevo
    // no se utiliza. Se inicializa para que no dé error.
    $oPermActiv = [];
}


if (!empty($id_ubi) && $id_ubi != 1) {
    $oCasa = Ubi::newUbi($id_ubi);
    $nombre_ubi = $oCasa->getNombre_ubi();
    // Puede ser que haga referencia a un id_ubi desaparecido.
    if (empty($nombre_ubi)) {
        $nombre_ubi = _("ya no existe: cambiarlo");
    } else {
        $delegacion = $oCasa->getDl();
        $region = $oCasa->getRegion();
        $sv = $oCasa->isSv();
        $sf = $oCasa->isSf();
    }
} else {
    if ($id_ubi == 1 && $lugar_esp) {
        $nombre_ubi = $lugar_esp;
    }
    if (!$id_ubi && !$lugar_esp) {
        $nombre_ubi = _("sin determinar");
    }
}

$oDesplDelegacionesOrg = DelegacionDropdown::delegacionesURegiones($isfsv, $Bdl, 'dl_org');
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
$oDesplRepeticion->setOpciones($aOpciones);
$oDesplRepeticion->setNombre('id_repeticion');
$oDesplRepeticion->setOpcion_sel($id_repeticion);

$oHash = new Hash();
$camposForm = 'status!dl_org!f_fin!f_ini!h_fin!h_ini!id_repeticion!id_ubi!lugar_esp!mod!nivel_stgr!nom_activ!nombre_ubi!observ!precio!id_tarifa!publicado!plazas';
$camposNo = 'mod';
if ($Qmod === 'nuevo' or $Qmod === 'cambiar_tipo') {
    $camposForm .= '!extendida!iactividad_val!iasistentes_val!inom_tipo_val!isfsv_val';
    $camposNo .= '!id_tipo_activ';
} else {
    $camposForm .= '!sactividad!sasistentes!snom_tipo';

}
$oHash->setCamposForm($camposForm);
$oHash->setCamposNo($camposNo);
$a_camposHidden = array(
    'id_tipo_activ' => $id_tipo_activ,
    'id_activ' => $Qid_activ,
    'ssfsv' => $ssfsv,
//		'mod' => $Qmod,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new Hash();
$oHash1->setUrl(ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select_ubi.php');
$oHash1->setCamposForm('dl_org!ssfsv!isfsv');
$h = $oHash1->linkSinVal();

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setSfsvAll(TRUE);
$oActividadTipo->setQue('buscar');
$oActividadTipo->setId_tipo_activ($id_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($sasistentes);
$oActividadTipo->setActividad($sactividad);
$oActividadTipo->setNom_tipo($snom_tipo);
if (($Qmod !== 'cambiar_tipo') && (strlen($id_tipo_activ) > 3)) {
    $extendida = TRUE;
} else {
    $extendida = FALSE;
}

$procesos_installed = ConfigGlobal::is_app_installed('procesos');

$status_txt = $a_status[$status];
$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'obj' => addslashes($obj),
    'godossiers' => $godossiers,
    'alt' => $alt,
    'dos' => $dos,
    'oPermActiv' => $oPermActiv,
    'permiso_des' => $permiso_des,
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
    'mod' => $Qmod,
    'oActividadTipo' => $oActividadTipo,
    'extendida' => $extendida,
    'id_tipo_activ' => $id_tipo_activ,
    'web' => ConfigGlobal::getWeb(),
    'web_icons' => ConfigGlobal::getWeb_icons(),
    'procesos_installed' => $procesos_installed,
    'locale_us' => ConfigGlobal::is_locale_us(),
];

$oView = new ViewTwig('actividades/controller');
$oView->renderizar('actividad_form.html.twig', $a_campos);