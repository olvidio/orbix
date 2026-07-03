<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComTxtUpdate $useCase */
$useCase = DependencyResolver::get(ListasComTxtUpdate::class);


$clave = (string)(FilterPostGet::post('clave') ?? FilterPostGet::get('clave') ?? '');
$idioma = (string)(FilterPostGet::post('idioma') ?? FilterPostGet::get('idioma') ?? '');
$comunicacion = (string)(FilterPostGet::post('comunicacion') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma, $comunicacion));
