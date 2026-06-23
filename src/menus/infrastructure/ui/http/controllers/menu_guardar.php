<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\menus\application\MenuGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_grupmenu = input_int($_POST, 'filtro_grupo');
$Qid_menu = input_int($_POST, 'id_menu');
$Qok = input_string($_POST, 'ok');
$Qorden = input_string($_POST, 'orden');
$Qtxt_menu = input_string($_POST, 'txt_menu');
$Qparametros = input_string($_POST, 'parametros');
$Qid_metamenu = input_int($_POST, 'id_metamenu');
$rawPermMenu = filter_post('perm_menu', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qperm_menu = [];
if (is_array($rawPermMenu)) {
    foreach ($rawPermMenu as $perm) {
        if (is_string($perm)) {
            $Qperm_menu[] = $perm;
        }
    }
}

/** @var MenuGuardar $menuGuardar */
$menuGuardar = DependencyResolver::get(MenuGuardar::class);
$error_txt = $menuGuardar($Qid_grupmenu, $Qid_menu, $Qok, $Qorden, $Qtxt_menu, $Qparametros, $Qid_metamenu, $Qperm_menu);

ContestarJson::enviar($error_txt, 'ok');
