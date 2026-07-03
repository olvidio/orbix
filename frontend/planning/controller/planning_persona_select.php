<?php
namespace frontend\planning\controller;

use frontend\planning\helpers\PlanningPostInput;
use frontend\planning\helpers\PlanningPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Lista de personas que cumplen los filtros del formulario anterior
 * (`planning_persona_que`).
 *
 * Migrado desde `apps/planning/controller/planning_persona_select.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$Qid_sel = PlanningPostInput::postString('id_sel');
$Qscroll_id = PlanningPostInput::postString('scroll_id');
$Qobj_pau = PlanningPostInput::postString('obj_pau');
$Qna = PlanningPostInput::postString('na');
$Qperiodo = PlanningPostInput::postString('periodo');
$Qyear = PlanningPostInput::postString('year');
$Qempiezamin = PlanningPostInput::postString('empiezamin');
$Qempiezamax = PlanningPostInput::postString('empiezamax');
$Qapellido1 = PlanningPostInput::postString('apellido1');
$Qapellido2 = PlanningPostInput::postString('apellido2');
$Qnombre = PlanningPostInput::postString('nombre');
$Qcentro = PlanningPostInput::postString('centro');
$QsaWhere = '';
$QsaOperador = '';
$QsaWhereCtr = '';
$QsaOperadorCtr = '';

if (isset($_POST['stack'])) {
    $stack = PlanningPostInput::postInt('stack');
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qobj_pau = PlanningPayload::posicionString($oPosicion2->getParametro('obj_pau'), $Qobj_pau);
            $Qna = PlanningPayload::posicionString($oPosicion2->getParametro('na'), $Qna);
            $Qperiodo = PlanningPayload::posicionString($oPosicion2->getParametro('periodo'), $Qperiodo);
            $Qyear = PlanningPayload::posicionString($oPosicion2->getParametro('year'), $Qyear);
            $Qempiezamin = PlanningPayload::posicionString($oPosicion2->getParametro('empiezamin'), $Qempiezamin);
            $Qempiezamax = PlanningPayload::posicionString($oPosicion2->getParametro('empiezamax'), $Qempiezamax);
            $Qid_sel = PlanningPayload::posicionString($oPosicion2->getParametro('id_sel'), $Qid_sel);
            $Qscroll_id = PlanningPayload::posicionString($oPosicion2->getParametro('scroll_id'), $Qscroll_id);
            $QsaWhere = PlanningPayload::posicionString($oPosicion2->getParametro('saWhere'));
            $QsaOperador = PlanningPayload::posicionString($oPosicion2->getParametro('saOperador'));
            $QsaWhereCtr = PlanningPayload::posicionString($oPosicion2->getParametro('saWhereCtr'));
            $QsaOperadorCtr = PlanningPayload::posicionString($oPosicion2->getParametro('saOperadorCtr'));
            $oPosicion2->olvidar($stack);

            $aWhereDecoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($QsaWhere), true);
            $aWhere = is_array($aWhereDecoded) ? $aWhereDecoded : [];
            $aOperadorDecoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($QsaOperador), true);
            $aOperador = is_array($aOperadorDecoded) ? $aOperadorDecoded : [];
            $aWhereCtrDecoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($QsaWhereCtr), true);
            $aWhereCtr = is_array($aWhereCtrDecoded) ? $aWhereCtrDecoded : [];
            $aOperadorCtrDecoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($QsaOperadorCtr), true);
            $aOperadorCtr = is_array($aOperadorCtrDecoded) ? $aOperadorCtrDecoded : [];
            $Qapellido1 = PlanningPayload::whereString($aWhere, 'apellido1', $Qapellido1);
            if (str_starts_with($Qapellido1, '^')) {
                $Qapellido1 = substr($Qapellido1, 1);
            }
            $Qapellido2 = PlanningPayload::whereString($aWhere, 'apellido2', $Qapellido2);
            if (str_starts_with($Qapellido2, '^')) {
                $Qapellido2 = substr($Qapellido2, 1);
            }
            $Qnombre = PlanningPayload::whereString($aWhere, 'nom', $Qnombre);
            if (str_starts_with($Qnombre, '^')) {
                $Qnombre = substr($Qnombre, 1);
            }
            $Qcentro = PlanningPayload::whereString($aWhereCtr, 'nombre_ubi', $Qcentro);
            $idTablaWhere = PlanningPayload::whereString($aWhere, 'id_tabla');
            if (str_starts_with($idTablaWhere, 'p')) {
                $Qna = substr($idTablaWhere, 1);
            }
        }
    }
}
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionForRecordar(($aGoBack ?? \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost()), $Qid_sel, $Qscroll_id));


$aWhere = [
    'situacion' => 'A',
    '_ordre' => 'apellido1,apellido2,nom',
];
$aOperador = [];
$aWhereCtr = [];
$aOperadorCtr = [];
if ($Qapellido1 !== '') {
    $aWhere['apellido1'] = '^' . $Qapellido1;
    $aOperador['apellido1'] = 'sin_acentos';
}
if ($Qapellido2 !== '') {
    $aWhere['apellido2'] = '^' . $Qapellido2;
    $aOperador['apellido2'] = 'sin_acentos';
}
if ($Qnombre !== '') {
    $aWhere['nom'] = '^' . $Qnombre;
    $aOperador['nom'] = 'sin_acentos';
}
if ($Qcentro !== '') {
    $nom_ubi = str_replace('+', '\\+', $Qcentro);
    $aWhereCtr['nombre_ubi'] = $nom_ubi;
    $aOperadorCtr['nombre_ubi'] = 'sin_acentos';
}
if ($Qna !== '') {
    $aWhere['id_tabla'] = 'p' . $Qna;
}
$QsaWhere = \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode(json_encode($aWhere, JSON_THROW_ON_ERROR));
$QsaOperador = \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode(json_encode($aOperador, JSON_THROW_ON_ERROR));
$QsaWhereCtr = \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode(json_encode($aWhereCtr, JSON_THROW_ON_ERROR));
$QsaOperadorCtr = \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode(json_encode($aOperadorCtr, JSON_THROW_ON_ERROR));

$postPayload = [
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'apellido1' => $Qapellido1,
    'apellido2' => $Qapellido2,
    'nombre' => $Qnombre,
    'centro' => $Qcentro,
];

$apiData = PostRequest::getDataFromUrl('/src/planning/planning_persona_select_data', $postPayload);
$cPersonas = PlanningPayload::personasFromPayload($apiData['personas'] ?? null);

$aGoBack = [
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'nombre' => $Qnombre,
    'apellido1' => $Qapellido1,
    'apellido2' => $Qapellido2,
    'centro' => $Qcentro,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'saWhere' => $QsaWhere,
    'saOperador' => $QsaOperador,
    'saWhereCtr' => $QsaWhereCtr,
    'saOperadorCtr' => $QsaOperadorCtr,
];
$oPosicion->setParametros($aGoBack, 1);

$a_botones = [
    ['txt' => _("marcar todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)"],
    ['txt' => _("desmarcar todos"), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)"],
    ['txt' => _("vista tabla"), 'click' => "fnjs_ver_planning(\"#seleccionados\",1)"],
    ['txt' => _("vista para imprimir"), 'click' => "fnjs_planning_print(\"#seleccionados\")"],
    ['txt' => _("ver actividades"), 'click' => "fnjs_actividades(\"#seleccionados\")"],
];
$a_cabeceras = [
    _("tipo"),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    _("centro"),
];

$i = 0;
/** @var array<int|string, mixed> $a_valores */
$a_valores = [];
if ($Qid_sel !== '') {
    $a_valores['select'] = $Qid_sel;
}
if ($Qscroll_id !== '') {
    $a_valores['scroll_id'] = $Qscroll_id;
}
foreach ($cPersonas as $row) {
    $i++;
    $id_nom = $row['id_nom'];
    $id_tabla = $row['id_tabla'];
    $nom = $row['pref_apellidos_nombre'];
    $ctr_o_dl = $row['centro_o_dl'];
    $condicion_2 = urlencode("Where id_nom='" . $id_nom . "'");

    $aQuery = [
        'id_nom' => $id_nom,
        'condicion' => $condicion_2,
        'obj_pau' => $Qobj_pau,
        'id_tabla' => $id_tabla,
    ];
    array_walk($aQuery, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerEmptyOnNull']);
    $pagina = HashFront::link('frontend/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores[$i] = [
        'sel' => (string) $id_nom,
        1 => $id_tabla,
        2 => ['ira' => $pagina, 'valor' => $nom],
        3 => $ctr_o_dl,
    ];
}

$oHash = new HashFront();
$oHash->setcamposNo('sel!scroll_id!modelo!que!id_dossier!sSeleccionados');
$oHash->setArraycamposHidden([
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'pau' => 'p',
]);

$oTabla = new Lista();
$oTabla->setId_tabla('planning_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$urlVer = 'frontend/planning/controller/planning_persona_ver.php';

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'num_personas' => $i,
    'urlVer' => $urlVer,
];

$oView = new ViewNewPhtml('frontend\planning\controller');
$oView->renderizar('planning_persona_select.phtml', $a_campos);
