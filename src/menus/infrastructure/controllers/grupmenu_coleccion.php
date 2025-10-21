<?php

use src\menus\application\GrupMenuColeccionUseCase;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$GrupMenusCollecion = new GrupMenuColeccionUseCase();
$cGrupMenus = $GrupMenusCollecion();

$a_valores = [];
$i = 0;
foreach ($cGrupMenus as $GrupMenu) {
    $i++;
    $a_valores[$i]['sel'] = $GrupMenu->getId_grupmenu();
    $a_valores[$i]['grupmenu'] = $GrupMenu->getGrup_menu();
    $a_valores[$i]['orden'] = $GrupMenu->getOrden();

}

$data['a_valores'] = $a_valores;

// envía una Response
ContestarJson::enviar($error_txt, $data);
