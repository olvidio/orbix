<?php

use src\encargossacd\application\PropuestasListaEncData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var PropuestasListaEncData $useCase */
$useCase = DependencyResolver::get(PropuestasListaEncData::class);
ContestarJson::enviar('', $useCase->execute((int) (\src\shared\domain\helpers\FilterPostGet::post('filtro_ctr') ?? \src\shared\domain\helpers\FilterPostGet::get('filtro_ctr') ?? 0)));
