<?php

use src\menus\application\MenuEliminar;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_menu = (integer)filter_input(INPUT_POST, 'id_menu');

$MenuEliminar = new MenuEliminar();
$error_txt = $MenuEliminar($Qid_menu, $Qid_menu);

ContestarJson::enviar($error_txt, 'ok');