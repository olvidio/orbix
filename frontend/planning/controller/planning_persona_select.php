<?php

namespace frontend\planning\controller;

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use function frontend\shared\helpers\urlsafe_b64decode;
use function frontend\shared\helpers\urlsafe_b64encode;

/**
 * Lista de personas que cumplen los filtros del formulario anterior
 * (`planning_persona_que`).
 *
 * Migrado desde `apps/planning/controller/planning_persona_select.php`
 * (slice 2 de la migracion del modulo planning).
 */
require_once("frontend/shared/global_header_front.inc");


/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qid_sel = '';
$Qscroll_id = '';
$Qobj_pau = '';
$Qna = '';
$Qperiodo = '';
$Qyear = '';
$Qempiezamin = '';
$Qempiezamax = '';

$postPayload = [];

if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    $QsaWhere = '';
    $QsaOperador = '';
    $QsaWhereCtr = '';
    $QsaOperadorCtr = '';
    $aWhere = [];
    $aOperador = [];
    $aWhereCtr = [];
    $aOperadorCtr = [];
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack((int)$stack)) {
            $Qobj_pau = (string)$oPosicion2->getParametro('obj_pau');
            $Qna = (string)$oPosicion2->getParametro('na');
            $Qperiodo = (string)$oPosicion2->getParametro('periodo');
            $Qyear = (string)$oPosicion2->getParametro('year');
            $Qempiezamin = (string)$oPosicion2->getParametro('empiezamin');
            $Qempiezamax = (string)$oPosicion2->getParametro('empiezamax');
            $QsaWhere = (string)$oPosicion2->getParametro('saWhere');
            $QsaOperador = (string)$oPosicion2->getParametro('saOperador');
            $QsaWhereCtr = (string)$oPosicion2->getParametro('saWhereCtr');
            $QsaOperadorCtr = (string)$oPosicion2->getParametro('saOperadorCtr');
            $Qid_sel = (string)$oPosicion2->getParametro('id_sel');
            $Qscroll_id = (string)$oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar((int)$stack);

            $aWhere = json_decode(urlsafe_b64decode($QsaWhere), true) ?? [];
            $aOperador = json_decode(urlsafe_b64decode($QsaOperador), true) ?? [];
            $aWhereCtr = json_decode(urlsafe_b64decode($QsaWhereCtr), true) ?? [];
            $aOperadorCtr = json_decode(urlsafe_b64decode($QsaOperadorCtr), true) ?? [];
        }
    }
    $postPayload = [
        'stack' => $stack,
        'obj_pau' => $Qobj_pau,
        'saWhere' => $QsaWhere ?? '',
        'saOperador' => $QsaOperador ?? '',
        'saWhereCtr' => $QsaWhereCtr ?? '',
        'saOperadorCtr' => $QsaOperadorCtr ?? '',
    ];
} else {
    $Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
    $Qna = (string)filter_input(INPUT_POST, 'na');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    $Qapellido2 = (string)filter_input(INPUT_POST, 'apellido2');
    $Qnombre = (string)filter_input(INPUT_POST, 'nombre');
    $Qcentro = (string)filter_input(INPUT_POST, 'centro');

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
    $QsaWhere = urlsafe_b64encode(json_encode($aWhere, JSON_THROW_ON_ERROR));
    $QsaOperador = urlsafe_b64encode(json_encode($aOperador, JSON_THROW_ON_ERROR));
    $QsaWhereCtr = urlsafe_b64encode(json_encode($aWhereCtr, JSON_THROW_ON_ERROR));
    $QsaOperadorCtr = urlsafe_b64encode(json_encode($aOperadorCtr, JSON_THROW_ON_ERROR));

    $postPayload = [
        'obj_pau' => $Qobj_pau,
        'na' => $Qna,
        'apellido1' => $Qapellido1,
        'apellido2' => $Qapellido2,
        'nombre' => $Qnombre,
        'centro' => $Qcentro,
    ];
}

$apiData = PostRequest::getDataFromUrl('/src/planning/planning_persona_select_data', $postPayload);
$cPersonas = $apiData['personas'] ?? [];

$aGoBack = [
    'obj_pau' => $Qobj_pau,
    'na' => $Qna,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'saWhere' => $QsaWhere ?? '',
    'saOperador' => $QsaOperador ?? '',
    'saWhereCtr' => $QsaWhereCtr ?? '',
    'saOperadorCtr' => $QsaOperadorCtr ?? '',
];
$oPosicion->setParametros($aGoBack, 1);

$a_botones = [
    ['txt' => _("vista tabla"), 'click' => "fnjs_ver_planning(\"#seleccionados\",1)"],
    ['txt' => _("vista grid"), 'click' => "fnjs_ver_planning(\"#seleccionados\",3)"],
    ['txt' => _("vista para imprimir"), 'click' => "fnjs_planning_print(\"#seleccionados\")"],
    ['txt' => _("ver actividades"), 'click' => "fnjs_actividades(\"#seleccionados\")"],
];
$a_cabeceras = [
    _("tipo"),
    ['name' => _("nombre y apellidos"), 'formatter' => 'clickFormatter'],
    _("centro"),
];

$i = 0;
$a_valores = [];
if (!empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (!empty($Qscroll_id)) {
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
    array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
    $pagina = HashFront::link('frontend/personas/controller/home_persona.php?' . http_build_query($aQuery));

    $a_valores[$i]['sel'] = "$id_nom";
    $a_valores[$i][1] = $id_tabla;
    $a_valores[$i][2] = ['ira' => $pagina, 'valor' => $nom];
    $a_valores[$i][3] = $ctr_o_dl;
}

$oHash = new HashFront();
$oHash->setcamposNo('sel!scroll_id!modelo!que!id_dossier');
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
