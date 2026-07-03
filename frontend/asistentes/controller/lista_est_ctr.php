<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */

$Qn_agd = (string)filter_input(INPUT_POST, 'n_agd');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros([
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
], \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$oPosicion->setParametros([
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
], 1);

$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_est_ctr_data', $campos));

echo $oPosicion->mostrar_left_slide(1);
echo \frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'lista_html');
