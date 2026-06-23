<?php

use src\encargossacd\application\ListasBData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasBData $useCase */
$useCase = DependencyResolver::get(ListasBData::class);


$sf = (int)(filter_post('sf') ?? filter_get('sf') ?? 0);

ContestarJson::enviar('', $useCase->execute($sf));
