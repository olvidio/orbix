<?php

use src\encargossacd\application\PropuestasListaSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var PropuestasListaSacdData $useCase */
$useCase = DependencyResolver::get(PropuestasListaSacdData::class);

$sel = (string) (FilterPostGet::post('sel') ?? FilterPostGet::get('sel') ?? '');
ContestarJson::enviar('', $useCase->execute($sel));
