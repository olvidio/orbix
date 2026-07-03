<?php


/**
 * Endpoint backend: formulario anual de gastos/aportaciones (`casa_ec_gastos_form`).
 */

use src\casas\application\CasaEcGastosFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year'),
    'id_cdc' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
];

/** @var CasaEcGastosFormData $useCase */
$useCase = DependencyResolver::get(CasaEcGastosFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
