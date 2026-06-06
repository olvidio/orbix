<?php
/**
 * Endpoint backend: listado de actividades por casa (`casa_actividades_lista`).
 */

use src\casas\application\CasaActividadesListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'id_cdc' => input_string_list($_POST, 'id_cdc'),
    'periodo' => input_string($_POST, 'periodo'),
    'year' => input_string($_POST, 'year'),
    'empiezamin' => input_string($_POST, 'empiezamin'),
    'empiezamax' => input_string($_POST, 'empiezamax'),
];

/** @var CasaActividadesListaData $useCase */
$useCase = DependencyResolver::get(CasaActividadesListaData::class);
ContestarJson::enviar('', $useCase->execute($input));
