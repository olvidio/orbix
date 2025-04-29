<?php

use permisos\model\PermDl;
use src\usuarios\application\repositories\PermMenuRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');

$oCuadros = new PermDl();
$PermMenuRepository = new PermMenuRepository();
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