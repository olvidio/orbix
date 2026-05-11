<?php
/**
 * Endpoint backend: elimina una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
];
$result = CartaPresentacionEliminar::execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'] ?? 'error', '');
}
