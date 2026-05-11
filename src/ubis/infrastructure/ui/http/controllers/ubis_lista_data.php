<?php

use src\ubis\application\UbisListaData;
use src\shared\web\ContestarJson;

$Qnombre_ubi = (string)filter_input(INPUT_POST, 'nombre_ubi');
$data = UbisListaData::execute($Qnombre_ubi);
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
]);
