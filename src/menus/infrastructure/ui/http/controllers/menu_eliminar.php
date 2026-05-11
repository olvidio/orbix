<?php

use src\menus\application\MenuEliminar;
use src\shared\web\ContestarJson;

$Qid_menu = (integer)filter_input(INPUT_POST, 'id_menu');

$MenuEliminar = new MenuEliminar();
$error_txt = $MenuEliminar($Qid_menu, $Qid_menu);

ContestarJson::enviar($error_txt, 'ok');