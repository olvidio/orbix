<?php


/**
 * Endpoint backend: crea / actualiza una `CartaPresentacion`.
 */

use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_ubi' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    'id_direccion' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_direccion'),
    'pres_nom' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pres_nom'),
    'pres_telf' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pres_telf'),
    'pres_mail' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pres_mail'),
    'zona' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'zona'),
    'observ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ'),
];

/** @var CartaPresentacionUpdate $useCase */
$useCase = DependencyResolver::get(CartaPresentacionUpdate::class);
$result = $useCase->execute($input);
if ($result['ok']) {
    ContestarJson::enviar('', '');
} else {
    ContestarJson::enviar($result['mensaje'], '');
}
