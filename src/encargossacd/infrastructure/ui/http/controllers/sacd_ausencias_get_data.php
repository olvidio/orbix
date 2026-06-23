<?php

use src\encargossacd\application\SacdAusenciasGetData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdAusenciasGetData $useCase */
$useCase = DependencyResolver::get(SacdAusenciasGetData::class);


$id_nom = (int)(filter_post('id_nom') ?? filter_get('id_nom') ?? 0);
$historial = (int)(filter_post('historial') ?? filter_get('historial') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_nom, $historial));
