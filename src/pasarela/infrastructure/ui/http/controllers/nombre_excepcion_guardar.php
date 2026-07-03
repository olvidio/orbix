<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\NombreExcepcionGuardar;

$id_tipo_activ = (string)FilterPostGet::post('id_tipo_activ');
$valor = (string)FilterPostGet::post('valor');

/** @var NombreExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(NombreExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
