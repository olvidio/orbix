<?php

use src\encargossacd\application\SacdSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdSelectData $useCase */
$useCase = DependencyResolver::get(SacdSelectData::class);


$filtro_sacd = (string)(filter_post('filtro_sacd') ?? filter_get('filtro_sacd') ?? '');
$id_nom = (int)(filter_post('id_nom') ?? filter_get('id_nom') ?? 0);

ContestarJson::enviar('', $useCase->execute($filtro_sacd, $id_nom));
