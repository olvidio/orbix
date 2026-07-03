<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ActivacionExcepcionEliminar;

$id_tipo_activ = (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ');

/** @var ActivacionExcepcionEliminar $useCase */
$useCase = DependencyResolver::get(ActivacionExcepcionEliminar::class);

$error_txt = $useCase->execute($id_tipo_activ);
ContestarJson::enviar($error_txt, 'ok');
