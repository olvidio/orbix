<?php

use permisos\model\PermDl;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use web\ContestarJson;

$Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');

$oCuadros = new PermDl();
$PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
$oGrupoGrupoPermMenu = $PermMenuRepository->getPermMenus(array('id_usuario' => $Qid_usuario));

$a_cabeceras = [array('name' => _("oficina o grupo"), 'width' => '350')];
$a_botones = [array('txt' => _("quitar"), 'click' => "fnjs_del_perm_menu(\"#permisos_menu\")")];

$i = 0;
$a_valores = [];
foreach ($oGrupoGrupoPermMenu as $oPermMenu) {
    $i++;

    $id_item = $oPermMenu->getId_item();
    $menu_perm = $oPermMenu->getMenu_perm();

    $a_valores[$i]['sel'] = "$Qid_usuario#$id_item";
    $a_valores[$i][1] = $oCuadros->lista_txt($menu_perm);
}

$data = ['a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
];

$error_txt = '';

ContestarJson::enviar($error_txt, $data);