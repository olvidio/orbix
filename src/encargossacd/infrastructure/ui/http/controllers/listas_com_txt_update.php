<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComTxtUpdate $useCase */
$useCase = DependencyResolver::get(ListasComTxtUpdate::class);


$clave = (string)(filter_post('clave') ?? filter_get('clave') ?? '');
$idioma = (string)(filter_post('idioma') ?? filter_get('idioma') ?? '');
$comunicacion = (string)(filter_post('comunicacion') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma, $comunicacion));
