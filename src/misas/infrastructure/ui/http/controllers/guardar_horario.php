<?php

use Illuminate\Http\JsonResponse;
use src\misas\application\GuardarHorarioTarea;

$result = GuardarHorarioTarea::execute([
    'id_item_h' => filter_input(INPUT_POST, 'id_item_h'),
    't_start' => filter_input(INPUT_POST, 't_start'),
    't_end' => filter_input(INPUT_POST, 't_end'),
]);

if ($result['error'] === '') {
    $jsondata = ['success' => true, 'mensaje' => 'ok'];
} else {
    $jsondata = ['success' => false, 'mensaje' => $result['error']];
}

(new JsonResponse($jsondata))->send();
exit();
