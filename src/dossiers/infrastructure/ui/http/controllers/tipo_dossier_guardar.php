<?php

use src\dossiers\application\TipoDossierGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoDossierGuardar $useCase */
$useCase = DependencyResolver::get(TipoDossierGuardar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
