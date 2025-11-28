<?php

use src\menus\application\MenuMover;
use web\ContestarJson;

$Qid_menu = (integer)filter_input(INPUT_POST, 'id_menu');
$Qgm_new = (string)filter_input(INPUT_POST, 'gm_new');

$error_txt = '';

if (empty($Qgm_new)) {
    $error_txt .= _("hay un error. Debe indicar el destino");
} else {
    $MenuMover = new MenuMover();
    $error_txt = $MenuMover($Qid_menu, $Qgm_new);
}

ContestarJson::enviar($error_txt, 'ok');