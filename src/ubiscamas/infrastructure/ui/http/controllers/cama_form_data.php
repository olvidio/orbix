<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\application\CamaFormData;

$input = array_merge($_GET, $_POST);

/** @var CamaFormData $useCase */
$useCase = DependencyResolver::get(CamaFormData::class);
ContestarJson::enviar('', $useCase->execute($input));
