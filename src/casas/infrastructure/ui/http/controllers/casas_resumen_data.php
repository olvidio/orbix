<?php


/**
 * Endpoint backend: resumen económico de casas (`casas_resumen_data`).
 */

use src\casas\application\CasasResumenData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'que' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'que'),
    'cdc_sel' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'cdc_sel'),
    'id_cdc' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasasResumenData $useCase */
$useCase = DependencyResolver::get(CasasResumenData::class);
ContestarJson::enviar('', $useCase->execute($input));
