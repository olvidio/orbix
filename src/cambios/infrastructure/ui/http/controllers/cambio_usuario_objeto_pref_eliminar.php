<?php
/**
 * Endpoint JSON: elimina un `CambioUsuarioObjetoPref`.
 *
 * Sucesor de la rama `eliminar` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefEliminar;
use web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    strtok((string)$a_sel[0], '#'); // id_usuario
    $id_item_usuario_objeto = (int)strtok('#');
} else {
    $id_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');
}

$input = ['id_item_usuario_objeto' => $id_item_usuario_objeto];
$result = CambioUsuarioObjetoPrefEliminar::execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
