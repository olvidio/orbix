<?php

use src\menus\application\MenuGuardar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_grupmenu = FuncTablasSupport::inputInt($_POST, 'filtro_grupo');
$Qid_menu = FuncTablasSupport::inputInt($_POST, 'id_menu');
$Qok = FuncTablasSupport::inputString($_POST, 'ok');
$Qorden = FuncTablasSupport::inputString($_POST, 'orden');
$Qtxt_menu = FuncTablasSupport::inputString($_POST, 'txt_menu');
$Qparametros = FuncTablasSupport::inputString($_POST, 'parametros');
$Qid_metamenu = FuncTablasSupport::inputInt($_POST, 'id_metamenu');
$rawPermMenu = FilterPostGet::post('perm_menu', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
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
