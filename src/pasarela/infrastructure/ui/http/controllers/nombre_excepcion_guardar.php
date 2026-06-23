<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\NombreExcepcionGuardar;

$id_tipo_activ = (string)filter_post('id_tipo_activ');
$valor = (string)filter_post('valor');

/** @var NombreExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(NombreExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
