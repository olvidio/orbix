<?php

use src\encargossacd\application\ListasComSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComSacdData $useCase */
$useCase = DependencyResolver::get(ListasComSacdData::class);


$sel = (string)(filter_input(INPUT_POST, 'sel') ?? filter_input(INPUT_GET, 'sel') ?? '');

ContestarJson::enviar('', $useCase->execute($sel));
