<?php

use src\encargossacd\application\ListasAData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasAData $useCase */
$useCase = DependencyResolver::get(ListasAData::class);


$sf = (int)(filter_post('sf') ?? filter_get('sf') ?? 0);

ContestarJson::enviar('', $useCase->execute($sf));
