<?php

use frontend\actividadestudios\helpers\ActividadestudiosListaSupport;
use frontend\actividadestudios\helpers\MatriculasListaPayload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Listado de matrículas (dossier). Datos vía PostRequest a matriculas_lista_data.
 * Sin `use src\...`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = [];
foreach (['mod', 'year', 'periodo', 'empiezamin', 'empiezamax', 'id_dossier', 'permiso', 'obj_pau', 'queSel', 'pau'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros($navState, $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);

$aviso = '';
$Qmod = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'mod'));
$Qyear = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'year'));
$Qperiodo = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'periodo'));
$Qempiezamin = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamin'));
$Qempiezamax = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamax'));

if ($Qperiodo === '') {
    $Qperiodo = 'curso_ca';
}

$oPeriodo = Periodo::conCalendarioDesdeBackend();
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$lista = MatriculasListaPayload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_lista_data', [
    'inicioIso' => $inicioIso,
    'finIso' => $finIso,
])));

$titulo = $lista['titulo'];
$msg_err = $lista['msg_err'];
$a_valores = ActividadestudiosListaSupport::valores($lista['a_valores'], $Qid_sel, $Qscroll_id);

$a_cabeceras = [
    _('alumno'),
    _('ctr'),
    _('dl'),
    _('actividad'),
    _('asignatura'),
    _('preceptor'),
    _('nota'),
];

$a_botones = [
    ['txt' => _('ver asignaturas ca'), 'click' => 'fnjs_ver_ca(this.form)'],
    ['txt' => _('borrar matrícula'), 'click' => 'fnjs_borrar(this.form)'],
];

$oHash = new HashFront();
$oHash->setCamposNo('sel!mod!pau!scroll_id!id_sel!id_pau');
$a_camposHidden = [
    'id_dossier' => 3005,
    'permiso' => 3,
    'obj_pau' => 'Actividad',
    'queSel' => 'asig',
];
$oHash->setArraycamposHidden($a_camposHidden);

if ($msg_err !== '') {
    echo \frontend\shared\helpers\PayloadCoercion::string($msg_err);
}

$oTabla = new Lista();
$oTabla->setId_tabla('mtr_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$txt_eliminar = _('¿Está seguro que desea borrar todas las matrículas seleccionadas?');

$boton = "<input type='button' value='" . _('buscar') . "' onclick='fnjs_buscar()' >";
$aOpciones = [
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '---------',
    'curso_ca' => _('curso ca'),
    'separador1' => '---------',
    'otro' => _('otro'),
];
$oFormP = new frontend\shared\web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(\src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_('periodo de selección de actividades')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHashPeriodo = new HashFront();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$oHashPeriodo->setArraycamposHidden([]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'mod' => $Qmod,
    'oTabla' => $oTabla,
    'titulo' => $titulo,
    'aviso' => $aviso,
    'txt_eliminar' => $txt_eliminar,
    'oFormP' => $oFormP,
    'oHashPeriodo' => $oHashPeriodo,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('matriculas.phtml', $a_campos);
