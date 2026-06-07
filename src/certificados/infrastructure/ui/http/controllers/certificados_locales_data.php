<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

require_once 'frontend/shared/global_header_front.inc';

/** @var LocalRepositoryInterface $localRepository */
$localRepository = DependencyResolver::get(LocalRepositoryInterface::class);

$error = '';
$data = [];
try {
    $data = ['a_locales' => $localRepository->getArrayLocales()];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
