<?php

use Psr\Container\ContainerInterface;

use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\shared\web\ContestarJson;

/** @var ContainerInterface $container */
$container = $GLOBALS['container'];
/** @var \src\asistentes\application\FormActividadesDeUnaPersonaData $useCase */
$useCase = $container->get(FormActividadesDeUnaPersonaData::class);
$data = $useCase->build($_POST);
if (isset($data['error'])) {
    $error = is_string($data['error']) ? $data['error'] : '';
    ContestarJson::enviar($error, 'none');
    return;
}
ContestarJson::enviar('', $data);
