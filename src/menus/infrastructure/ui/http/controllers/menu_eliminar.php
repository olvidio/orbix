<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\menus\application\MenuEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_menu = input_int($_POST, 'id_menu');

/** @var MenuEliminar $menuEliminar */
$menuEliminar = DependencyResolver::get(MenuEliminar::class);
$error_txt = $menuEliminar($Qid_menu);

ContestarJson::enviar($error_txt, 'ok');
