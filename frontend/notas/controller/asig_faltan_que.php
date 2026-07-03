<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$restored = \frontend\shared\helpers\ListNavSupport::restoreSelectionFromStackPost();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !\frontend\shared\helpers\ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : \frontend\shared\helpers\ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : \frontend\shared\helpers\ListNavSupport::scrollIdFromPost();
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));



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
$chk_c1 = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qc1) ? 'checked' : '';
$Qc2 = (string)filter_input(INPUT_POST, 'c2');
$chk_c2 = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qc2) ? 'checked' : '';
$Qpersonas_n = (string)filter_input(INPUT_POST, 'personas_n');
$chk_n = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_n) ? 'checked' : '';
$Qpersonas_agd = (string)filter_input(INPUT_POST, 'personas_agd');
$chk_agd = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpersonas_agd) ? 'checked' : '';

$Qid_asignatura = (string)filter_input(INPUT_POST, 'id_asignatura');

$Qlista = (string)filter_input(INPUT_POST, 'lista');
$chk_lista = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qlista) ? 'checked' : '';

$dAsig = PostRequest::getDataFromUrl('/src/asignaturas/asignaturas_con_separador_data', []);
$aOpciones = NotasFormSupport::desplegableOpciones($dAsig['a_opciones'] ?? []);
$oDesplAsignaturas = new Desplegable('', $aOpciones, '', true);
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setOpcion_sel($Qid_asignatura);

$oHash = new HashFront();
$oHash->setcamposChk('personas_n!personas_agd!c1!c2!lista');
$oHash->setCamposForm('numero!b_c');

$oHash1 = new HashFront();
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
