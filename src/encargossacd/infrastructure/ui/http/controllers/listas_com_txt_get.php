<?php

use src\encargossacd\application\ListasComTxtGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComTxtGet $useCase */
$useCase = DependencyResolver::get(ListasComTxtGet::class);


$clave = (string)(FilterPostGet::post('clave') ?? FilterPostGet::get('clave') ?? '');
$idioma = (string)(FilterPostGet::post('idioma') ?? FilterPostGet::get('idioma') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma));
