<?php

use src\menus\application\GrupMenuColeccionUseCase;
use web\ContestarJson;

$error_txt = '';

$GrupMenusCollecion = new GrupMenuColeccionUseCase();
$cGrupMenus = $GrupMenusCollecion();

$a_valores = [];
$i = 0;
foreach ($cGrupMenus as $GrupMenu) {
    $i++;
    $a_valores[$i]['sel'] = $GrupMenu->getId_grupmenu();
    $a_valores[$i]['grupmenu'] = $GrupMenu->getGrup_menu();
    $a_valores[$i]['orden'] = $GrupMenu->getOrdenVo()->value();

}

$data['a_valores'] = $a_valores;

// envía una Response
ContestarJson::enviar($error_txt, $data);
