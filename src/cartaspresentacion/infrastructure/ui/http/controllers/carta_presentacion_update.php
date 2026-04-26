<?php
/**
 * Endpoint backend: crea / actualiza una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionUpdate;
use frontend\shared\web\ContestarJson;

$input = [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
    'pres_nom' => (string)filter_input(INPUT_POST, 'pres_nom'),
    'pres_telf' => (string)filter_input(INPUT_POST, 'pres_telf'),
    'pres_mail' => (string)filter_input(INPUT_POST, 'pres_mail'),
    'zona' => (string)filter_input(INPUT_POST, 'zona'),
    'observ' => (string)filter_input(INPUT_POST, 'observ'),
];
$result = CartaPresentacionUpdate::execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'] ?? 'error', '');
}
