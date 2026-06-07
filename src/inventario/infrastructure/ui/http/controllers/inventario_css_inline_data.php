<?php

use src\inventario\application\InventarioCssInlineData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var InventarioCssInlineData $useCase */
$useCase = DependencyResolver::get(InventarioCssInlineData::class);
$data = $useCase->execute();

ContestarJson::enviar('', $data);
