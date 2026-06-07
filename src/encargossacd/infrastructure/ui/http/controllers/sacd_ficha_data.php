<?php

use src\encargossacd\application\SacdFichaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdFichaData $useCase */
$useCase = DependencyResolver::get(SacdFichaData::class);


$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_nom));
