<?php
/**
 * Endpoint backend: resumen económico de casas (`casas_resumen_data`).
 */

use src\casas\application\CasasResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'que' => input_string($_POST, 'que'),
    'cdc_sel' => input_int($_POST, 'cdc_sel'),
    'id_cdc' => input_string_list($_POST, 'id_cdc'),
    'year' => input_string($_POST, 'year'),
    'periodo' => input_string($_POST, 'periodo'),
    'empiezamin' => input_string($_POST, 'empiezamin'),
    'empiezamax' => input_string($_POST, 'empiezamax'),
];

/** @var CasasResumenData $useCase */
$useCase = DependencyResolver::get(CasasResumenData::class);
ContestarJson::enviar('', $useCase->execute($input));
