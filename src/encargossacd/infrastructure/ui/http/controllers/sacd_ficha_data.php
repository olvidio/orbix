<?php

use src\encargossacd\application\SacdFichaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var SacdFichaData $useCase */
$useCase = DependencyResolver::get(SacdFichaData::class);


$id_nom = (int)(FilterPostGet::post('id_nom') ?? FilterPostGet::get('id_nom') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_nom));
