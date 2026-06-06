<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrPage;

/** @var ZonaCtrPage $useCase */
$useCase = DependencyResolver::get(ZonaCtrPage::class);
ContestarJson::enviar('', $useCase->getData());
