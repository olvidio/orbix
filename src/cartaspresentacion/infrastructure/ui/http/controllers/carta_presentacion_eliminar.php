<?php
/**
 * Endpoint backend: elimina una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$input = [
    'id_ubi' => input_int($_POST, 'id_ubi'),
    'id_direccion' => input_int($_POST, 'id_direccion'),
];

/** @var CartaPresentacionEliminar $useCase */
$useCase = DependencyResolver::get(CartaPresentacionEliminar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
