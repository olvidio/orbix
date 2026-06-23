<?php

use src\encargossacd\application\ListasComTxtGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComTxtGet $useCase */
$useCase = DependencyResolver::get(ListasComTxtGet::class);


$clave = (string)(filter_post('clave') ?? filter_get('clave') ?? '');
$idioma = (string)(filter_post('idioma') ?? filter_get('idioma') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma));
