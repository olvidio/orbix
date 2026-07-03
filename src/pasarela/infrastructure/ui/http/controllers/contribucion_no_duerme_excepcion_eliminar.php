<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeExcepcionEliminar;

$id_tipo_activ = (string)FilterPostGet::post('id_tipo_activ');

/** @var ContribucionNoDuermeExcepcionEliminar $useCase */
$useCase = DependencyResolver::get(ContribucionNoDuermeExcepcionEliminar::class);

$error_txt = $useCase->execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
