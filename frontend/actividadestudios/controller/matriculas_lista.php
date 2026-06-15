<?php
/**
 * Listado de matrículas (dossier). Datos vía PostRequest a matriculas_lista_data.
 * Sin `use src\...`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use function frontend\shared\helpers\strtoupper_dlb;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qid_sel = null;
$Qscroll_id = null;
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));


$aviso = '';
$Qmod = tessera_imprimir_string(filter_input(INPUT_POST, 'mod'));
$Qyear = tessera_imprimir_string(filter_input(INPUT_POST, 'year'));
$Qperiodo = tessera_imprimir_string(filter_input(INPUT_POST, 'periodo'));
$Qempiezamin = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamin'));
$Qempiezamax = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamax'));

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

$lista = actividadestudios_matriculas_lista_from_payload(actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/matriculas_lista_data', [
    'inicioIso' => $inicioIso,
    'finIso' => $finIso,
])));

$titulo = $lista['titulo'];
$msg_err = $lista['msg_err'];
$a_valores = actividadestudios_lista_valores($lista['a_valores'], $Qid_sel, $Qscroll_id);

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
    actividadestudios_echo_string($msg_err);
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
$oFormP->setTitulo(strtoupper_dlb(_('periodo de selección de actividades')));
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
