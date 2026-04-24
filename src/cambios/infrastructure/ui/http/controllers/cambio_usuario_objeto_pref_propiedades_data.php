<?php
/**
 * Endpoint JSON: listado de propiedades configurables del objeto indicado,
 * preseleccionadas segun las preferencias ya guardadas.
 *
 * Sucesor de la rama `propiedades` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData;
use web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'objeto' => (string)filter_input(INPUT_POST, 'objeto'),
    'id_item_usuario_objeto' => (int)filter_input(INPUT_POST, 'id_item_usuario_objeto'),
];

$result = CambioUsuarioObjetoPrefPropiedadesData::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
