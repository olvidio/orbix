<?php

use src\encargossacd\application\PropuestasListaSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var PropuestasListaSacdData $useCase */
$useCase = DependencyResolver::get(PropuestasListaSacdData::class);

$sel = (string) (\src\shared\domain\helpers\FilterPostGet::post('sel') ?? \src\shared\domain\helpers\FilterPostGet::get('sel') ?? '');
ContestarJson::enviar('', $useCase->execute($sel));
