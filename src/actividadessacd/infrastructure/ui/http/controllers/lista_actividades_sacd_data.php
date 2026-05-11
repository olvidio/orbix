<?php
/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos junto con los sacd encargados y los flags de permiso.
 *
 * El HTML de la tabla se construye en el controller frontend
 * `frontend/actividadessacd/controller/activ_sacd.php` + vista.
 */

use src\actividadessacd\application\ListaActividadesSacdData;
use src\shared\web\ContestarJson;

$input = [
    'tipo' => (string)filter_input(INPUT_POST, 'tipo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = ListaActividadesSacdData::execute($input);
ContestarJson::enviar('', $data);
