<?php

use src\dossiers\application\TipoDossierEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoDossierEliminar $useCase */
$useCase = DependencyResolver::get(TipoDossierEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
