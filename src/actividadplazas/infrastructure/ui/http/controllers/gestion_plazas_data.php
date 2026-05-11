<?php
/**
 * Endpoint backend: devuelve los datos del cuadro de gestion de
 * plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo)
 * para que el controller frontend monte el `frontend\shared\web\TablaEditable`.
 */

use src\actividadplazas\application\GestionPlazasData;
use src\shared\web\ContestarJson;

$input = [
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'sactividad2' => (string)filter_input(INPUT_POST, 'sactividad2'),
];

$data = GestionPlazasData::execute($input);
ContestarJson::enviar('', $data);
