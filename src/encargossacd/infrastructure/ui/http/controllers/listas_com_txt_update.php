<?php

use src\encargossacd\application\ListasComTxtUpdate;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComTxtUpdate $useCase */
$useCase = DependencyResolver::get(ListasComTxtUpdate::class);


$clave = (string)(\src\shared\domain\helpers\FilterPostGet::post('clave') ?? \src\shared\domain\helpers\FilterPostGet::get('clave') ?? '');
$idioma = (string)(\src\shared\domain\helpers\FilterPostGet::post('idioma') ?? \src\shared\domain\helpers\FilterPostGet::get('idioma') ?? '');
$comunicacion = (string)(\src\shared\domain\helpers\FilterPostGet::post('comunicacion') ?? '');

ContestarJson::enviar('', $useCase->execute($clave, $idioma, $comunicacion));
