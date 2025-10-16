<?php

use src\menus\application\ListaGrupMenus;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$ListaGrupMenus = new ListaGrupMenus();
$data = $ListaGrupMenus();

// envía una Response
ContestarJson::enviar($error_txt, $data);
