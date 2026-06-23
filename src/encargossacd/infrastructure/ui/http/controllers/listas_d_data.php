<?php

use src\encargossacd\application\ListasDData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasDData $useCase */
$useCase = DependencyResolver::get(ListasDData::class);


$sf = (int)(filter_post('sf') ?? filter_get('sf') ?? 0);

ContestarJson::enviar('', $useCase->execute($sf));
