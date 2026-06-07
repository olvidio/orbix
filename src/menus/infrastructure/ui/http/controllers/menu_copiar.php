<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\menus\application\MenuCopiar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_menu = input_int($_POST, 'id_menu');
$Qgm_new = input_string($_POST, 'gm_new');

$error_txt = '';

if (empty($Qgm_new)) {
    $error_txt .= _("hay un error. Debe indicar el destino");
} else {
    /** @var MenuCopiar $menuCopiar */
    $menuCopiar = DependencyResolver::get(MenuCopiar::class);
    $error_txt = $menuCopiar($Qid_menu, $Qgm_new);
}

ContestarJson::enviar($error_txt, 'ok');
