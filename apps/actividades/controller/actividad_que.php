<?php
/**
 * Esta página muestra un formulario con las opciones para escoger la actividad.
 *
 * Se le pasan las var:
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @ajax        21/8/2007.
 *
 */

use actividades\model\ActividadLugar;
use actividades\model\ActividadTipo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorTipoDeActividad;
use core\ConfigGlobal;
use core\ViewTwig;
use procesos\model\entity\GestorActividadFase;
use ubis\model\entity\GestorDelegacion;
use usuarios\model\entity\Usuario;
use web\Hash;
use web\PeriodoQue;
use web\Posicion;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo de vuelta y le paso la referencia del stack donde está la información.
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qmodo = (string)filter_input(INPUT_POST, 'modo');
$Qmodo = empty($Qmodo) ? 'buscar' : $Qmodo;
$Qque = (string)filter_input(INPUT_POST, 'que');
$Qstatus = (integer)filter_input(INPUT_POST, 'status');
$Qid_tipo_activ = (integer)filter_input(INPUT_POST, 'id_tipo_activ');
$Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qnom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qfases_on = (array)filter_input(INPUT_POST, 'fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qfases_off = (array)filter_input(INPUT_POST, 'fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qlistar_asistentes = (string)filter_input(INPUT_POST, 'listar_asistentes');
$Qpublicado = (integer)filter_input(INPUT_POST, 'publicado');

$isfsv = core\ConfigGlobal::mi_sfsv();
$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm_oficina('vcsd')) || ($_SESSION['oPerm']->have_perm_oficina('des'))) {
    $permiso_des = TRUE;
    $ssfsv = '';
} else {
    if ($isfsv === 1) {
        $ssfsv = 'sv';
    }
    if ($isfsv === 2) {
        $ssfsv = 'sf';
    }
}

$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');
$Qextendida = (string)filter_input(INPUT_POST, 'extendida');

$Qsactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');
if (!empty($Qsactividad2)) {
    $Qextendida = TRUE;
}
$extendida = is_true($Qextendida) ? TRUE : FALSE;

$oActividadTipo = new ActividadTipo();
$oActividadTipo->setPerm_jefe($permiso_des);
$oActividadTipo->setId_tipo_activ($Qid_tipo_activ);
$oActividadTipo->setSfsv($ssfsv);
$oActividadTipo->setAsistentes($Qsasistentes);
if ($extendida) {
    $oActividadTipo->setActividad2Digitos($Qsactividad2);
} else {
    $oActividadTipo->setActividad($Qsactividad);
}
$oActividadTipo->setNom_tipo($Qsnom_tipo);


if (empty($Qstatus)) {
    $Qstatus = ActividadAll::STATUS_ACTUAL;
}

$Qisfsv = substr($Qid_tipo_activ, 0, 1);
$mi_dele = ConfigGlobal::mi_delef($Qisfsv);
$oGesDl = new GestorDelegacion();
$oDesplDelegacionesOrg = $oGesDl->getListaDelegacionesURegiones($Qisfsv);
$oDesplDelegacionesOrg->setNombre('dl_org');
$oDesplDelegacionesOrg->setOpcion_sel($Qdl_org);
if ($Qmodo === 'importar') {
    $oDesplDelegacionesOrg->setOpcion_no(array($mi_dele));
}
if ($Qmodo === 'publicar') {
    $oDesplDelegacionesOrg->setOpciones(array($mi_dele => $mi_dele));
    $oDesplDelegacionesOrg->setBlanco(false);
}
// para cambiar el listado de fases si no el la dl_propia
if (core\ConfigGlobal::is_app_installed('procesos')) {
    $oDesplDelegacionesOrg->setAction('fnjs_actualizar_fases();');
}


$oDesplFiltroLugar = $oGesDl->getListaDlURegionesFiltro($Qisfsv);
$oDesplFiltroLugar->setAction('fnjs_lugar()');
$oDesplFiltroLugar->setNombre('filtro_lugar');
$oDesplFiltroLugar->setOpcion_sel($Qfiltro_lugar);

$oDesplegableCasas = array();
if (!empty($Qfiltro_lugar)) {
    $oActividadLugar = new ActividadLugar();
    $oDesplegableCasas = $oActividadLugar->getLugaresPosibles($Qfiltro_lugar);
    if (!empty($Qid_ubi)) {
        $oDesplegableCasas->setOpcion_sel($Qid_ubi);
    }
}

$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'curso_crt' => _("curso crt"),
    'separador1' => '---------',
    'otro' => _("otro")
);
$oFormP = new PeriodoQue();
$oFormP->setFormName('modifica');
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);

$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);

$oHash = new Hash();
$oHash->setCamposForm('dl_org!empiezamax!empiezamin!filtro_lugar!extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!id_ubi!nom_activ!periodo!status!year!publicado');
$camposNo = 'id_ubi!nom_activ';
if (core\ConfigGlobal::is_app_installed('procesos')) {
    $camposNo .= '!fases_on!fases_off';
}
$oHash->setcamposNo($camposNo);
$a_camposHidden = array(
    'modo' => $Qmodo,
    'listar_asistentes' => $Qlistar_asistentes,
    'que' => $Qque
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new Hash();
$oHash1->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_tipo_get.php');
$oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
$h = $oHash1->linkSinVal();

$aQuery = array('que' => $Qque, 'sactividad' => $Qsactividad, 'sasistentes' => $Qsasistentes);
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}
$Link_borrar = web\Hash::link('apps/actividades/controller/actividad_que.php?' . http_build_query($aQuery));

switch ($Qmodo) {
    case 'importar':
        $titulo = ucfirst(_("buscar actividad de otras dl para importar"));
        break;
    case 'publicar':
        $titulo = ucfirst(_("buscar actividades de mi dl para publicar"));
        break;
    default:
        $titulo = ucfirst(_("buscar actividad"));
}

/* a continuación distinguimos el caso habitual en que 
vamos a la página actividad_select.php
de los casos particulares de algunos listados, 
en que vamos directamente a
las páginas que los generan*/
switch ($Qque) {
    case "list_activ" :
    case "list_activ_compl" :
        $accion = core\ConfigGlobal::getWeb() . '/apps/actividades/controller/lista_activ.php';
        /*es el caso de querer sacar tablas
        de un grupo de actividades*/
        break;
    case "list_cjto" :
    case "list_cjto_sacd" :
        $accion = core\ConfigGlobal::getWeb() . '/apps/asistentes/controller/lista_asis_conjunto_activ.php';
        /*es el caso de querer sacar
        los asistentes o cargos
        de un conjunto de actividades*/
        break;
    default;
        $accion = core\ConfigGlobal::getWeb() . '/apps/actividades/controller/actividad_select.php';
        /*es el caso de todo el resto
        de listados que pasan por un listado
        previo con los links */
        break;
}

$perm_jefe = FALSE;
if ($_SESSION['oConfig']->is_jefeCalendario()
    || (($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) && ConfigGlobal::mi_sfsv() === 1)
    || ($_SESSION['oPerm']->have_perm_oficina('admin_sf') && ConfigGlobal::mi_sfsv() === 2)
) {
    $perm_jefe = TRUE;
}
$oActividadTipo->setPerm_jefe($perm_jefe);
$oActividadTipo->setSfsvAll(TRUE);


$oUsuario = new Usuario(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
$perm_ctr = FALSE;
if (!$oUsuario->isRolePau('ctr')) {
    $perm_ctr = TRUE;
}

$val_status_1 = ActividadAll::STATUS_PROYECTO;
$chk_status_1 = ($Qstatus === $val_status_1) ? "checked='true'" : '';
$val_status_2 = ActividadAll::STATUS_ACTUAL;
$chk_status_2 = ($Qstatus === $val_status_2) ? "checked='true'" : '';
$val_status_3 = ActividadAll::STATUS_TERMINADA;
$chk_status_3 = ($Qstatus === $val_status_3) ? "checked='true'" : '';
$val_status_4 = ActividadAll::STATUS_BORRABLE;
$chk_status_4 = ($Qstatus === $val_status_4) ? "checked='true'" : '';
$val_status_9 = ActividadAll::STATUS_ALL;
$chk_status_9 = ($Qstatus === $val_status_9) ? "checked='true'" : '';

//////////// PROCESOS /////////////////
$proceso_installed = FALSE;
$url_actualizar_fases = '';
$h_actualizar_fases = '';
$CuadrosFasesOn = '';
$CuadrosFasesOff = '';
if (core\ConfigGlobal::is_app_installed('procesos')) {
    $proceso_installed = TRUE;
    $url_actualizar_fases = ConfigGlobal::getWeb() . '/apps/procesos/controller/actividad_que_fases_ajax.php';
    $oHash1 = new Hash();
    $oHash1->setUrl($url_actualizar_fases);
    $oHash1->setCamposForm('salida!dl_propia!id_tipo_activ');
    $h_actualizar_fases = $oHash1->linkSinVal();

    $dl_propia = ($Qdl_org === $mi_dele);
    $GesTiposActiv = new GestorTipoDeActividad();
    // Para limitar las opciones:
    if (empty($Qid_tipo_activ)) {
        $Qid_tipo_activ = ConfigGlobal::mi_sfsv();
    }
    $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ, $dl_propia);
    $oGesFases = new GestorActividadFase();
    $aFases = $oGesFases->getArrayFasesProcesos($aTiposDeProcesos);
    foreach ($aFases as $descripcion => $id_fase) {
        if (in_array($id_fase, $Qfases_on, true)) {
            $chk = 'checked';
        } else {
            $chk = '';
        }
        $CuadrosFasesOn .= "<input type='checkbox' name='fases_on[]' value='$id_fase' $chk /> $descripcion";
        if (in_array($id_fase, $Qfases_off, true)) {
            $chk = 'checked';
        } else {
            $chk = '';
        }
        $CuadrosFasesOff .= "<input type='checkbox' name='fases_off[]' value='$id_fase' $chk /> $descripcion";
    }
}

$chk_publicado_1 = '';
$chk_publicado_2 = '';
$chk_publicado_3 = '';
switch ($Qpublicado) {
    case 1:
        $chk_publicado_1 = "checked='true'";
        break;
    case 2:
        $chk_publicado_2 = "checked='true'";
        break;
    case 3:
    default:
        $chk_publicado_3 = "checked='true'";
        break;
}


$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'accion' => $accion,
    'Qid_ubi' => $Qid_ubi,
    'Qnom_activ' => $Qnom_activ,
    'h' => $h,
    'titulo' => $titulo,
    'oDesplFiltroLugar' => $oDesplFiltroLugar,
    'oDesplegableCasas' => $oDesplegableCasas,
    'oDesplDelegacionesOrg' => $oDesplDelegacionesOrg,
    'oFormP' => $oFormP,
    'oActividadTipo' => $oActividadTipo,
    'extendida' => $extendida,
    'Link_borrar' => $Link_borrar,
    'perm_ctr' => $perm_ctr,
    'val_status_1' => $val_status_1,
    'chk_status_1' => $chk_status_1,
    'val_status_2' => $val_status_2,
    'chk_status_2' => $chk_status_2,
    'val_status_3' => $val_status_3,
    'chk_status_3' => $chk_status_3,
    'val_status_4' => $val_status_4,
    'chk_status_4' => $chk_status_4,
    'val_status_9' => $val_status_9,
    'chk_status_9' => $chk_status_9,
    'proceso_installed' => $proceso_installed,
    'url_actualizar_fases' => $url_actualizar_fases,
    'h_actualizar_fases' => $h_actualizar_fases,
    'CuadrosFasesOn' => $CuadrosFasesOn,
    'CuadrosFasesOff' => $CuadrosFasesOff,
    'mi_dele' => $mi_dele,
    'locale_us' => ConfigGlobal::is_locale_us(),
    'chk_publicado_1' => $chk_publicado_1,
    'chk_publicado_2' => $chk_publicado_2,
    'chk_publicado_3' => $chk_publicado_3,
];

$oView = new ViewTwig('actividades/controller');
$oView->renderizar('actividad_que.html.twig', $a_campos);
