<?php

use src\encargossacd\application\SacdAusenciasGetData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdAusenciasGetData $useCase */
$useCase = DependencyResolver::get(SacdAusenciasGetData::class);


$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);
$historial = (int)(filter_input(INPUT_POST, 'historial') ?? filter_input(INPUT_GET, 'historial') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_nom, $historial));
