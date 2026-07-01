<?php

use src\encargossacd\application\PropuestasListaEncData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PropuestasListaEncData $useCase */
$useCase = DependencyResolver::get(PropuestasListaEncData::class);
ContestarJson::enviar('', $useCase->execute((int) (filter_post('filtro_ctr') ?? filter_get('filtro_ctr') ?? 0)));
