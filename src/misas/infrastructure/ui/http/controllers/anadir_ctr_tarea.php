<?php

use Illuminate\Http\JsonResponse;
use src\misas\application\AnadirCtrTarea;

$result = AnadirCtrTarea::execute([
    'que' => filter_input(INPUT_POST, 'que'),
    'id_ubi' => filter_input(INPUT_POST, 'id_ubi'),
    'id_tarea' => filter_input(INPUT_POST, 'id_tarea'),
    'id_item' => filter_input(INPUT_POST, 'id_item'),
]);

if ($result['error'] === '') {
    $jsondata = ['success' => true, 'mensaje' => 'ok'];
} else {
    $jsondata = ['success' => false, 'mensaje' => $result['error']];
}

(new JsonResponse($jsondata))->send();
exit();
