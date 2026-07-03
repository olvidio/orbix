<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: crea / actualiza una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'id_direccion' => FuncTablasSupport::inputInt($_POST, 'id_direccion'),
    'pres_nom' => FuncTablasSupport::inputString($_POST, 'pres_nom'),
    'pres_telf' => FuncTablasSupport::inputString($_POST, 'pres_telf'),
    'pres_mail' => FuncTablasSupport::inputString($_POST, 'pres_mail'),
    'zona' => FuncTablasSupport::inputString($_POST, 'zona'),
    'observ' => FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var CartaPresentacionUpdate $useCase */
$useCase = DependencyResolver::get(CartaPresentacionUpdate::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
