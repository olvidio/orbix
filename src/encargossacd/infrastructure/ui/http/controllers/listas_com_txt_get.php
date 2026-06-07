<?php

use src\encargossacd\application\ListasComTxtGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComTxtGet $useCase */
$useCase = DependencyResolver::get(ListasComTxtGet::class);


$clave = (string)(filter_input(INPUT_POST, 'clave') ?? filter_input(INPUT_GET, 'clave') ?? '');
$idioma = (string)(filter_input(INPUT_POST, 'idioma') ?? filter_input(INPUT_GET, 'idioma') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma));
