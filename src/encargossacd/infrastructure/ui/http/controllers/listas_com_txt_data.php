<?php

use src\encargossacd\application\ListasComTxtData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComTxtData $useCase */
$useCase = DependencyResolver::get(ListasComTxtData::class);


ContestarJson::enviar('', $useCase->execute());
