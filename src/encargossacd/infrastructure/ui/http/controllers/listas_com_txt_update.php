<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComTxtUpdate $useCase */
$useCase = DependencyResolver::get(ListasComTxtUpdate::class);


$clave = (string)(filter_input(INPUT_POST, 'clave') ?? filter_input(INPUT_GET, 'clave') ?? '');
$idioma = (string)(filter_input(INPUT_POST, 'idioma') ?? filter_input(INPUT_GET, 'idioma') ?? '');
$comunicacion = (string)(filter_input(INPUT_POST, 'comunicacion') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma, $comunicacion));
