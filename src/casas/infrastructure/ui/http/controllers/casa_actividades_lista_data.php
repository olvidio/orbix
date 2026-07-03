<?php


/**
 * Endpoint backend: listado de actividades por casa (`casa_actividades_lista`).
 */

use src\casas\application\CasaActividadesListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_cdc' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasaActividadesListaData $useCase */
$useCase = DependencyResolver::get(CasaActividadesListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
