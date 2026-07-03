<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: listado de actividades por casa (`casa_actividades_lista`).
 */

use src\casas\application\CasaActividadesListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_cdc' => FuncTablasSupport::inputStringList($_POST, 'id_cdc'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
];

/** @var CasaActividadesListaData $useCase */
$useCase = DependencyResolver::get(CasaActividadesListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
