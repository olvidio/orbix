<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\NombreExcepcionEliminar;

$id_tipo_activ = (string)FilterPostGet::post('id_tipo_activ');

/** @var NombreExcepcionEliminar $useCase */
$useCase = DependencyResolver::get(NombreExcepcionEliminar::class);

$error_txt = $useCase->execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
