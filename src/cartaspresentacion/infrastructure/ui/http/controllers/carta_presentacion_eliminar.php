<?php


/**
 * Endpoint backend: elimina una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'id_direccion' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_direccion'),
];

/** @var CartaPresentacionEliminar $useCase */
$useCase = DependencyResolver::get(CartaPresentacionEliminar::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
