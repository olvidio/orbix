<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdListaTot;

/** @var ZonaSacdListaTot $useCase */
$useCase = DependencyResolver::get(ZonaSacdListaTot::class);
ContestarJson::enviar('', $useCase->execute());
