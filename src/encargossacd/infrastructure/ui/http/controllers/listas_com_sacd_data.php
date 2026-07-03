<?php

use src\encargossacd\application\ListasComSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComSacdData $useCase */
$useCase = DependencyResolver::get(ListasComSacdData::class);


$sel = (string)(\src\shared\domain\helpers\FilterPostGet::post('sel') ?? \src\shared\domain\helpers\FilterPostGet::get('sel') ?? '');

ContestarJson::enviar('', $useCase->execute($sel));
