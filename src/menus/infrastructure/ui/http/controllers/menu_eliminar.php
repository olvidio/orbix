<?php

use src\menus\application\MenuEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_menu = FuncTablasSupport::inputInt($_POST, 'id_menu');

/** @var MenuEliminar $menuEliminar */
$menuEliminar = DependencyResolver::get(MenuEliminar::class);
$error_txt = $menuEliminar($Qid_menu);

ContestarJson::enviar($error_txt, 'ok');
