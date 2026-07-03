<?php

use src\encargossacd\application\ListasBData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasBData $useCase */
$useCase = DependencyResolver::get(ListasBData::class);


$sf = (int)(\src\shared\domain\helpers\FilterPostGet::post('sf') ?? \src\shared\domain\helpers\FilterPostGet::get('sf') ?? 0);

ContestarJson::enviar('', $useCase->execute($sf));
