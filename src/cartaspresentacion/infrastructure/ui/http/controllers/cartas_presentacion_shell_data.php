<?php

use src\cartaspresentacion\application\CartasPresentacionShellData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CartasPresentacionShellData $useCase */
$useCase = DependencyResolver::get(CartasPresentacionShellData::class);
ContestarJson::enviar('', $useCase->execute());
