<?php

use src\menus\application\GrupMenuListaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var GrupMenuListaUseCase $listaGrupMenus */
$listaGrupMenus = DependencyResolver::get(GrupMenuListaUseCase::class);
$data = $listaGrupMenus();

ContestarJson::enviar($error_txt, $data);
