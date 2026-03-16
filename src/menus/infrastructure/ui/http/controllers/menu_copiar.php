<?php

use src\menus\application\MenuCopiar;
use web\ContestarJson;

$Qid_menu = (integer)filter_input(INPUT_POST, 'id_menu');
$Qgm_new = (string)filter_input(INPUT_POST, 'gm_new');

$error_txt = '';

if (empty($Qgm_new)) {
    $error_txt .= _("hay un error. Debe indicar el destino");
} else {
    $MenuCopiar = new MenuCopiar();
    $error_txt = $MenuCopiar($Qid_menu, $Qgm_new);
}

ContestarJson::enviar($error_txt, 'ok');