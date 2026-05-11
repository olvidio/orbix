<?php
/**
 * Endpoint backend: devuelve el listado de actividades del tipo + periodo
 * elegidos, junto con los centros encargados de cada una y los flags de
 * permiso (ver / modificar / crear) para cada fila.
 *
 * El HTML de la tabla se construye en el controller frontend
 * `frontend/actividadescentro/controller/activ_ctr.php`.
 */

use src\actividadescentro\application\ListaActividadesCtrData;
use src\shared\web\ContestarJson;

$input = [
    'tipo' => (string)filter_input(INPUT_POST, 'tipo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];

$data = ListaActividadesCtrData::execute($input);
ContestarJson::enviar('', $data);
