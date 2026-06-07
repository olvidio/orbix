<?php

use src\encargossacd\application\SacdAusenciasJefeZonaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var SacdAusenciasJefeZonaData $useCase */
$useCase = DependencyResolver::get(SacdAusenciasJefeZonaData::class);


ContestarJson::enviar('', $useCase->execute());
