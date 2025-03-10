<?php

use usuarios\model\entity\PermMenu;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qmenu_perm = (array)filter_input(INPUT_POST, 'menu_perm', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$oUsuarioPerm = new PermMenu(array('id_item' => $Qid_item));
$oUsuarioPerm->setId_usuario($Qid_usuario);
//cuando el campo es menu_perm, se pasa un array que hay que convertirlo en número.
if (!empty($Qmenu_perm)) {
    $byte = 0;
    foreach ($Qmenu_perm as $bit) {
        $byte = $byte + $bit;
    }
    $oUsuarioPerm->setMenu_perm($byte);
}
if ($oUsuarioPerm->DBGuardar() === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $oUsuarioPerm->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');