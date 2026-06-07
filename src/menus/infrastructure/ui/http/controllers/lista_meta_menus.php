<?php

use src\menus\application\ListaMetaMenus;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var ListaMetaMenus $listaMetaMenus */
$listaMetaMenus = DependencyResolver::get(ListaMetaMenus::class);
$data = $listaMetaMenus();

ContestarJson::enviar($error_txt, $data);
