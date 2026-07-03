<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: resumen económico de casas (`casas_resumen_data`).
 */

use src\casas\application\CasasResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'que' => FuncTablasSupport::inputString($_POST, 'que'),
    'cdc_sel' => FuncTablasSupport::inputInt($_POST, 'cdc_sel'),
    'id_cdc' => FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasasResumenData $useCase */
$useCase = DependencyResolver::get(CasasResumenData::class);
ContestarJson::enviar('', $useCase->execute($input));
