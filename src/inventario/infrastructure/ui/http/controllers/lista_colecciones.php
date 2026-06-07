<?php

use src\inventario\application\ColeccionesOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ColeccionesOpcionesData $useCase */
$useCase = DependencyResolver::get(ColeccionesOpcionesData::class);
$data = $useCase->execute();

ContestarJson::enviar('', $data);
