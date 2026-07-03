<?php

use src\menus\application\MenuEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_menu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_menu');

/** @var MenuEliminar $menuEliminar */
$menuEliminar = DependencyResolver::get(MenuEliminar::class);
$error_txt = $menuEliminar($Qid_menu);

ContestarJson::enviar($error_txt, 'ok');
