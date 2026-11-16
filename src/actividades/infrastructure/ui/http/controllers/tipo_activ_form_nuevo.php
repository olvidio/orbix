<?php

use src\actividades\application\TipoActivFormNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivFormNuevo $useCase */
$useCase = DependencyResolver::get(TipoActivFormNuevo::class);
$html = $useCase->execute($_POST);

ContestarJson::enviar('', ['html' => $html]);
