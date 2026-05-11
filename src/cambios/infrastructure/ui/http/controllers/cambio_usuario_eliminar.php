<?php
/**
 * Endpoint backend: elimina `CambioUsuario` por la clave compuesta
 * `id_item_cambio#id_usuario#sfsv#aviso_tipo` recibida en `sel[]`.
 */

use src\cambios\application\CambioUsuarioEliminar;
use src\shared\web\ContestarJson;

$sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$result = CambioUsuarioEliminar::execute(['sel' => $sel]);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
