<?php

use src\encargossacd\application\ListasDData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasDData $useCase */
$useCase = DependencyResolver::get(ListasDData::class);


$sf = (int)(FilterPostGet::post('sf') ?? FilterPostGet::get('sf') ?? 0);

ContestarJson::enviar('', $useCase->execute($sf));
