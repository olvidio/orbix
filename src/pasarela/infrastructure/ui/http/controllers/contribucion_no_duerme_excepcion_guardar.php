<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeExcepcionGuardar;

$id_tipo_activ = (string)filter_post('id_tipo_activ');
$valor = (string)filter_post('valor');

/** @var ContribucionNoDuermeExcepcionGuardar $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeExcepcionGuardar::class);

$error_txt = $useCase->execute($id_tipo_activ, $valor);
ContestarJson::enviar($error_txt, 'ok');
