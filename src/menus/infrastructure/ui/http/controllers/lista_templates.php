<?php

use src\menus\application\ListaTemplatesMenus;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var ListaTemplatesMenus $listaTemplatesMenus */
$listaTemplatesMenus = DependencyResolver::get(ListaTemplatesMenus::class);
$data = $listaTemplatesMenus();

ContestarJson::enviar($error_txt, $data);
