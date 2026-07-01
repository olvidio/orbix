<?php

use src\encargossacd\application\PropuestasListaSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PropuestasListaSacdData $useCase */
$useCase = DependencyResolver::get(PropuestasListaSacdData::class);

$sel = (string) (filter_post('sel') ?? filter_get('sel') ?? '');
ContestarJson::enviar('', $useCase->execute($sel));
