<?php

use src\menus\application\MenuGuardar;
use web\ContestarJson;

$Qid_grupmenu = (integer)filter_input(INPUT_POST, 'filtro_grupo');
$Qid_menu = (integer)filter_input(INPUT_POST, 'id_menu');
$Qok = (string)filter_input(INPUT_POST, 'ok');
$Qorden = (string)filter_input(INPUT_POST, 'orden');
$Qtxt_menu = (string)filter_input(INPUT_POST, 'txt_menu');
$Qparametros = (string)filter_input(INPUT_POST, 'parametros');
$Qid_metamenu = (integer)filter_input(INPUT_POST, 'id_metamenu');
$Qperm_menu = (array)filter_input(INPUT_POST, 'perm_menu', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$MenuGuardar = new MenuGuardar();
$error_txt = $MenuGuardar($Qid_grupmenu, $Qid_menu, $Qok, $Qorden, $Qtxt_menu, $Qparametros, $Qid_metamenu, $Qperm_menu);

ContestarJson::enviar($error_txt, 'ok');