<?php

use frontend\shared\PostRequest;
use function frontend\shared\helpers\payload_string;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/asistentes_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
/** @var \frontend\shared\web\Posicion $oPosicion */

$Qn_agd = (string)filter_input(INPUT_POST, 'n_agd');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros([
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
], list_nav_id_sel_from_post(), list_nav_scroll_id_from_post()));


$oPosicion->setParametros([
    'n_agd' => $Qn_agd,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamax' => $Qempiezamax,
    'empiezamin' => $Qempiezamin,
], 1);

$campos = array_merge($_GET, $_POST);
$payload = asistentes_post_data(PostRequest::getDataFromUrl('/src/asistentes/lista_est_ctr_data', $campos));

echo $oPosicion->mostrar_left_slide(1);
echo payload_string($payload, 'lista_html');
