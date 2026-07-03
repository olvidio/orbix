<?php

use frontend\actividades\helpers\ActividadesPermSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Pantalla para escoger los filtros que determinan una busqueda de actividades.
 * Delega a actividad_select/lista_activ/lista_asis_conjunto_activ segun `que`.
 *
 * Migrado desde frontend/actividades/controller/actividad_que.php.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\shared\AppInstalled;
use frontend\actividades\helpers\ActividadStatusId;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\Posicion;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$stackFromPost = ListNavSupport::stackFromPost();
if ($stackFromPost !== 0) {
    $oPosicion2 = new Posicion();
    if ($oPosicion2->goStack($stackFromPost)) {
        $restoredSel = ListNavSupport::idSelForLista($oPosicion2->getParametro('id_sel'));
        if (!ListNavSupport::idSelIsEmpty($restoredSel)) {
            $Qid_sel = $restoredSel;
        }
        $restoredScroll = $oPosicion2->getParametro('scroll_id');
        if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
            $Qscroll_id = (string) $restoredScroll;
        }
        $oPosicion2->olvidar($stackFromPost);
    }
}

$Qmodo = (string)filter_input(INPUT_POST, 'modo');
$Qmodo = empty($Qmodo) ? 'buscar' : $Qmodo;
$Qque = (string)filter_input(INPUT_POST, 'que');
$Qstatus = (integer)filter_input(INPUT_POST, 'status');
$Qid_tipo_activ = PayloadCoercion::string(filter_input(INPUT_POST, 'id_tipo_activ'));
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

if ($stackFromPost !== 0) {
    ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    ListNavSupport::bootRecordar($oPosicion);
}
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildActividadQueReturnParametros([
    'modo' => $Qmodo,
    'que' => $Qque,
    'status' => $Qstatus,
    'id_tipo_activ' => $Qid_tipo_activ,
    'filtro_lugar' => $Qfiltro_lugar,
    'id_ubi' => $Qid_ubi,
    'nom_activ' => $Qnom_activ,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'fases_on' => $Qfases_on,
    'fases_off' => $Qfases_off,
    'publicado' => $Qpublicado,
    'listar_asistentes' => $Qlistar_asistentes,
    'id_sel' => $Qid_sel,
    'scroll_id' => $Qscroll_id,
]));

//Si vengo de vuelta y le paso la referencia del stack donde esta la informacion.

$isfsv = OrbixRuntime::miSfsv();
$ssfsv = '';
if (!ActividadesPermSupport::permDes()) {
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
$extendida = FuncTablasSupport::isTrue($Qextendida) ? TRUE : FALSE;


if (empty($Qstatus)) {
    $Qstatus = ActividadStatusId::ACTUAL;
}

$Qisfsv = substr($Qid_tipo_activ, 0, 1);
$mi_dele = OrbixRuntime::miDelef((string)$Qisfsv);

// El bloque de filtros extra (filtro_lugar, dl_org, publicada) se carga en
// cliente via AJAX al endpoint backend /src/actividades/actividad_que_filtros,
// que se encarga de comprobar perm_ctr y resolver desplegables de delegaciones
// y de lugares.
$url_filtros = AppUrlConfig::getPublicAppBaseUrl() . '/src/actividades/actividad_que_filtros';
$oHashFiltros = new HashFront();
$oHashFiltros->setUrl($url_filtros);
$oHashFiltros->setCamposForm('sfsv!modo!dl_org!filtro_lugar!id_ubi!publicado');
$h_filtros = $oHashFiltros->linkSinValParams();

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

$oHash = new HashFront();
$sCamposForm = 'dl_org!empiezamax!empiezamin!filtro_lugar!extendida!iactividad_val!iasistentes_val!id_tipo_activ!inom_tipo_val!isfsv_val!id_ubi!nom_activ!periodo!status!year';
if ($Qmodo !== 'importar') {
    $sCamposForm .= '!publicado';
}
$oHash->setCamposForm($sCamposForm);
$camposNo = 'id_ubi!nom_activ!dl_org!extendida!filtro_lugar';
if (AppInstalled::is('procesos')) {
    $camposNo .= '!fases_on!fases_off';
}
$oHash->setcamposNo($camposNo);
$a_camposHidden = array(
    'modo' => $Qmodo,
    'listar_asistentes' => $Qlistar_asistentes,
    'que' => $Qque
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new HashFront();
$oHash1->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/src/actividades/actividad_tipo_get');
$oHash1->setCamposForm('extendida!modo!salida!entrada!opcion_sel!isfsv');
$h = $oHash1->linkSinValParams();

$aQuery = array('que' => $Qque, 'sactividad' => $Qsactividad, 'sasistentes' => $Qsasistentes);
array_walk($aQuery, 'src\shared\domain\helpers\poner_empty_on_null');
$Link_borrar = HashFront::link('frontend/actividades/controller/actividad_que.php?' . http_build_query($aQuery));

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

/* a continuacion distinguimos el caso habitual en que
vamos a la pagina actividad_select.php
de los casos particulares de algunos listados,
en que vamos directamente a
las paginas que los generan*/
switch ($Qque) {
    case "list_activ" :
    case "list_activ_compl" :
        $accion = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/lista_activ.php';
        break;
    case "list_cjto" :
    case "list_cjto_sacd" :
        $accion = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/asistentes/controller/lista_asis_conjunto_activ.php';
        break;
    default:
        $accion = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividades/controller/actividad_select.php';
        break;
}

$data_que = PostRequest::getDataFromUrl('/src/actividades/actividad_que_datos', [
    'perm_jefe' => ActividadesPermSupport::permJefeTipoActiv() ? 't' : 'f',
    'id_tipo_activ' => $Qid_tipo_activ,
    'sfsv' => $ssfsv,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'sactividad2' => $Qsactividad2,
    'snom_tipo' => $Qsnom_tipo,
    'extendida' => $extendida ? 't' : '',
]);

$actividad_tipo_html = PayloadCoercion::string($data_que['actividad_tipo_html'] ?? '');

$chk_status_1 = ($Qstatus === ActividadStatusId::PROYECTO) ? "checked='true'" : '';
$chk_status_2 = ($Qstatus === ActividadStatusId::ACTUAL) ? "checked='true'" : '';
$chk_status_3 = ($Qstatus === ActividadStatusId::TERMINADA) ? "checked='true'" : '';
$chk_status_4 = ($Qstatus === ActividadStatusId::BORRABLE) ? "checked='true'" : '';
$chk_status_9 = ($Qstatus === ActividadStatusId::ALL) ? "checked='true'" : '';

//////////// PROCESOS /////////////////
// Los cuadros de fases_on/fases_off se pintan en cliente via AJAX al
// endpoint backend /src/procesos/actividad_que_fases_ajax. Aqui solo
// construimos la URL, el hash y los ids preseleccionados iniciales.
$proceso_installed = FALSE;
$url_actualizar_fases = '';
$h_actualizar_fases = '';
$fases_on_csv = '';
$fases_off_csv = '';
if (AppInstalled::is('procesos')) {
    $proceso_installed = TRUE;
    $url_actualizar_fases = AppUrlConfig::getPublicAppBaseUrl() . '/src/procesos/actividad_que_fases_ajax';
    $oHash1 = new HashFront();
    $oHash1->setUrl($url_actualizar_fases);
    $oHash1->setCamposForm('dl_propia!id_tipo_activ!selected');
    $h_actualizar_fases = $oHash1->linkSinValParams();

    if (empty($Qid_tipo_activ)) {
        $Qid_tipo_activ = OrbixRuntime::miSfsv();
    }
    $fases_on_csv = implode(',', array_filter(array_map('intval', $Qfases_on)));
    $fases_off_csv = implode(',', array_filter(array_map('intval', $Qfases_off)));
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'accion' => $accion,
    'Qid_ubi' => $Qid_ubi,
    'Qnom_activ' => $Qnom_activ,
    'h' => $h,
    'titulo' => $titulo,
    'oFormP' => $oFormP,
    'actividad_tipo_html' => $actividad_tipo_html,
    'extendida' => $extendida,
    'Link_borrar' => $Link_borrar,
    'val_status_1' => ActividadStatusId::PROYECTO,
    'chk_status_1' => $chk_status_1,
    'val_status_2' => ActividadStatusId::ACTUAL,
    'chk_status_2' => $chk_status_2,
    'val_status_3' => ActividadStatusId::TERMINADA,
    'chk_status_3' => $chk_status_3,
    'val_status_4' => ActividadStatusId::BORRABLE,
    'chk_status_4' => $chk_status_4,
    'val_status_9' => ActividadStatusId::ALL,
    'chk_status_9' => $chk_status_9,
    'proceso_installed' => $proceso_installed,
    'url_actualizar_fases' => $url_actualizar_fases,
    'h_actualizar_fases' => $h_actualizar_fases,
    'fases_on_csv' => $fases_on_csv,
    'fases_off_csv' => $fases_off_csv,
    'mi_dele' => $mi_dele,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'modo' => $Qmodo,
    'Qsfsv' => (int)$Qisfsv,
    'Qdl_org' => $Qdl_org,
    'Qfiltro_lugar' => $Qfiltro_lugar,
    'Qpublicado' => $Qpublicado,
    'url_filtros' => $url_filtros,
    'h_filtros' => $h_filtros,
];

$oView = new ViewNewTwig('frontend/actividades/controller');
$oView->renderizar('actividad_que.html.twig', $a_campos);
