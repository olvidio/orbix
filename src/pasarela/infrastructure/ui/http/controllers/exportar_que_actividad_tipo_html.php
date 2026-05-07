<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ExportarQueActividadTipoHtml;

$input = [
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'snom_tipo' => (string)filter_input(INPUT_POST, 'snom_tipo'),
];

$data = ExportarQueActividadTipoHtml::execute($input);
ContestarJson::enviar('', $data);
