<?php

use src\ubis\application\CentrosSListaData;
use web\ContestarJson;

$data = CentrosSListaData::execute();
if (isset($data['error'])) {
    ContestarJson::enviar((string)$data['error'], []);
    return;
}
ContestarJson::enviar('', [
    'a_cabeceras' => $data['a_cabeceras'],
    'a_valores' => $data['a_valores'],
    'num_total_s' => $data['num_total_s'],
]);
