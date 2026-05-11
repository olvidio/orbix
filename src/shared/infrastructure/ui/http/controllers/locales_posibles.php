<?php

use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';

$LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocalRepository->getArrayLocales();

$data['a_locales'] = $a_locales;

// envía una Response
ContestarJson::enviar($error_txt, $data);