<?php

use src\menus\application\MenuEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'menu_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_menu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_menu');

/** @var MenuEliminar $menuEliminar */
$menuEliminar = DependencyResolver::get(MenuEliminar::class);
$error_txt = $menuEliminar($Qid_menu);

ContestarJson::enviar($error_txt, 'ok');
