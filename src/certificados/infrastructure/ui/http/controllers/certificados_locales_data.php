<?php

use frontend\shared\web\ContestarJson;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

require_once 'frontend/shared/global_header_front.inc';

$error = '';
$data = [];
try {
    $LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
    $data = ['a_locales' => $LocalRepository->getArrayLocales()];
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
