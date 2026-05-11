<?php
/**
 * Endpoint JSON: devuelve los datos de una condicion por `id_item`
 * (si existe) y la lista de casas cuando la propiedad es `id_ubi`.
 *
 * Sucesor de la rama `condicion` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefItemData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'id_item' => (int)filter_input(INPUT_POST, 'id_item'),
    'objeto' => (string)filter_input(INPUT_POST, 'objeto'),
    'propiedad' => (string)filter_input(INPUT_POST, 'propiedad'),
];

$result = CambioUsuarioPropiedadPrefItemData::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
