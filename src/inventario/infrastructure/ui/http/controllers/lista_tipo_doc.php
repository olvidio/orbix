<?php

use src\inventario\application\TipoDocOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoDocOpcionesData $useCase */
$useCase = DependencyResolver::get(TipoDocOpcionesData::class);
$data = $useCase->execute();

ContestarJson::enviar('', $data);
