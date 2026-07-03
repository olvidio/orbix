<?php

use src\encargossacd\application\SacdSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var SacdSelectData $useCase */
$useCase = DependencyResolver::get(SacdSelectData::class);


$filtro_sacd = (string)(FilterPostGet::post('filtro_sacd') ?? FilterPostGet::get('filtro_sacd') ?? '');
$id_nom = (int)(FilterPostGet::post('id_nom') ?? FilterPostGet::get('id_nom') ?? 0);

ContestarJson::enviar('', $useCase->execute($filtro_sacd, $id_nom));
