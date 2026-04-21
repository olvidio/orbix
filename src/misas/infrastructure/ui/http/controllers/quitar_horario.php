<?php

use Illuminate\Http\JsonResponse;
use src\misas\application\QuitarHorarioPlantilla;

$result = QuitarHorarioPlantilla::execute([
    'id_item' => filter_input(INPUT_POST, 'id_item'),
]);

if ($result['error'] === '') {
    $jsondata = ['success' => true, 'mensaje' => 'ok'];
} else {
    $jsondata = ['success' => false, 'mensaje' => $result['error']];
}

(new JsonResponse($jsondata))->send();
exit();
