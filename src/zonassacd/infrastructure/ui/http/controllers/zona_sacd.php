<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdPage;

/** @var ZonaSacdPage $useCase */
$useCase = DependencyResolver::get(ZonaSacdPage::class);
ContestarJson::enviar('', $useCase->getData());
