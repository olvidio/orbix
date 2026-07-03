<?php

use src\menus\application\MenuCopiar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_menu = FuncTablasSupport::inputInt($_POST, 'id_menu');
$Qgm_new = FuncTablasSupport::inputString($_POST, 'gm_new');

$error_txt = '';

if (empty($Qgm_new)) {
    $error_txt .= _("hay un error. Debe indicar el destino");
} else {
    /** @var MenuCopiar $menuCopiar */
    $menuCopiar = DependencyResolver::get(MenuCopiar::class);
    $error_txt = $menuCopiar($Qid_menu, $Qgm_new);
}

ContestarJson::enviar($error_txt, 'ok');
