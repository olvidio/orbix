<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionExcepcionGuardar;

$id_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');
$valor = (string)\src\shared\domain\helpers\FilterPostGet::post('valor');

/** @var ActivacionExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(ActivacionExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
