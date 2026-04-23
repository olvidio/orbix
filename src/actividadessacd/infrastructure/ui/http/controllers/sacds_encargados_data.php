<?php
/**
 * Endpoint backend: devuelve los sacd encargados actuales de una
 * actividad en un array serializable, junto con los flags de permiso.
 */

use src\actividadessacd\application\SacdsEncargadosData;
use web\ContestarJson;

$input = [
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
];

$data = SacdsEncargadosData::execute($input);
ContestarJson::enviar('', $data);
