<?php

use src\encargossacd\application\ListasComSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComSacdData $useCase */
$useCase = DependencyResolver::get(ListasComSacdData::class);


$sel = (string)(filter_post('sel') ?? filter_get('sel') ?? '');

ContestarJson::enviar('', $useCase->execute($sel));
