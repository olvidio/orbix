<?php

use frontend\shared\PostRequest;
use function frontend\shared\helpers\payload_string;

require_once 'frontend/shared/global_header_front.inc';

/** @var \frontend\shared\web\Posicion $oPosicion */
$oPosicion->recordar();

$Qn_agd = (string)filter_input(INPUT_POST, 'n_agd');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

$oPosicion->setParametros([
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
], 1);

$campos = array_merge($_GET, $_POST);
$data = PostRequest::getDataFromUrl('/src/asistentes/lista_est_ctr_data', $campos);
$payload = is_array($data) ? $data : [];

echo $oPosicion->mostrar_left_slide(1);
echo payload_string($payload, 'lista_html');
