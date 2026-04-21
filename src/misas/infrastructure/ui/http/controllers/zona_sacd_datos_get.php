<?php

use Illuminate\Http\JsonResponse;
use src\misas\application\ZonaSacdDatosGet;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$Qid_sacd = (int)filter_input(INPUT_POST, 'id_sacd');

$result = ZonaSacdDatosGet::execute($Qid_zona, $Qid_sacd);

$jsondata = $result['payload'];
if ($result['error'] !== '') {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = $result['error'];
} else {
    $jsondata['success'] = true;
    $jsondata['mensaje'] = 'Tot correcte.';
}

(new JsonResponse($jsondata))->send();
