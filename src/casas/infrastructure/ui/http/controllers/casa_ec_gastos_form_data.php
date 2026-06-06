<?php
/**
 * Endpoint backend: formulario anual de gastos/aportaciones (`casa_ec_gastos_form`).
 */

use src\casas\application\CasaEcGastosFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'year' => input_int($_POST, 'year'),
    'id_cdc' => input_string_list($_POST, 'id_cdc'),
];

/** @var CasaEcGastosFormData $useCase */
$useCase = DependencyResolver::get(CasaEcGastosFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
