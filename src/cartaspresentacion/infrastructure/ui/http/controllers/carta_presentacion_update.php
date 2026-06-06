<?php
/**
 * Endpoint backend: crea / actualiza una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'id_direccion' => input_int($_POST, 'id_direccion'),
    'pres_nom' => input_string($_POST, 'pres_nom'),
    'pres_telf' => input_string($_POST, 'pres_telf'),
    'pres_mail' => input_string($_POST, 'pres_mail'),
    'zona' => input_string($_POST, 'zona'),
    'observ' => input_string($_POST, 'observ'),
];

/** @var CartaPresentacionUpdate $useCase */
$useCase = DependencyResolver::get(CartaPresentacionUpdate::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
