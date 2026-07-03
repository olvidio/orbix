<?php

use src\menus\application\MenuMover;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_menu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_menu');
$Qgm_new = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'gm_new');

$error_txt = '';

if (empty($Qgm_new)) {
    $error_txt .= _("hay un error. Debe indicar el destino");
} else {
    /** @var MenuMover $menuMover */
    $menuMover = DependencyResolver::get(MenuMover::class);
    $error_txt = $menuMover($Qid_menu, $Qgm_new);
}

ContestarJson::enviar($error_txt, 'ok');
