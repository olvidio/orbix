<?php

use frontend\shared\model\ViewNewPhtml;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use web\Desplegable;
use web\Hash;
use function core\is_true;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qnumero = (int)filter_input(INPUT_POST, 'numero');
$Qb_c = (string)filter_input(INPUT_POST, 'b_c');
if ($Qb_c === 'b') {
    $chk_b = 'checked';
    $chk_c = '';
} else {
    $chk_b = '';
    $chk_c = 'checked';
}
$Qc1 = (string)filter_input(INPUT_POST, 'c1');
$chk_c1 = is_true($Qc1) ? 'checked' : '';
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$chk_c2 = is_true($Qc2) ? 'checked' : '';
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$chk_n = is_true($Qpersonas_n) ? 'checked' : '';
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$chk_agd = is_true($Qpersonas_agd) ? 'checked' : '';

$Qid_asignatura = (string)filter_input(INPUT_POST, 'id_asignatura');

$Qlista = (string)filter_input(INPUT_POST, 'lista');
$chk_lista = is_true($Qlista) ? 'checked' : '';

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$aOpciones = $AsignaturaRepository->getArrayAsignaturasConSeparador();
$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setOpcion_sel($Qid_asignatura);

$oHash = new Hash();
$oHash->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash->setCamposForm('numero!b_c');

$oHash1 = new Hash();
$oHash1->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash1->setCamposForm('id_asignatura!b_c');

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'numero' => $Qnumero,
    'chk_n' => $chk_n,
    'chk_agd' => $chk_agd,
    'chk_b' => $chk_b,
    'chk_c' => $chk_c,
    'chk_c1' => $chk_c1,
    'chk_c2' => $chk_c2,
    'chk_lista' => $chk_lista,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('asig_faltan_que.phtml', $a_campos);
