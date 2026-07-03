<?php

use src\encargossacd\application\ListasComTxtGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComTxtGet $useCase */
$useCase = DependencyResolver::get(ListasComTxtGet::class);


$clave = (string)(\src\shared\domain\helpers\FilterPostGet::post('clave') ?? \src\shared\domain\helpers\FilterPostGet::get('clave') ?? '');
$idioma = (string)(\src\shared\domain\helpers\FilterPostGet::post('idioma') ?? \src\shared\domain\helpers\FilterPostGet::get('idioma') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma));
