<?php

use src\menus\application\GrupMenuColeccionUseCase;
use src\menus\application\MenusVisiblesPorGrupoMenuUseCase;
use src\shared\web\ContestarJson;

$error_txt = '';

$GrupMenusCollecion = new GrupMenuColeccionUseCase();
$cGrupMenus = $GrupMenusCollecion();
$menusVisiblesPorGrupo = new MenusVisiblesPorGrupoMenuUseCase();

$ambito = 'dl';
if (isset($_SESSION['oConfig']) && method_exists($_SESSION['oConfig'], 'getAmbito')) {
    $ambito = $_SESSION['oConfig']->getAmbito();
}

$a_valores = [];
$i = 0;
foreach ($cGrupMenus as $GrupMenu) {
    $i++;
    $id_gm = (int)$GrupMenu->getId_grupmenu();
    $a_valores[$i]['sel'] = $id_gm;
    $a_valores[$i]['grupmenu'] = $GrupMenu->getGrup_menu($ambito);
    $a_valores[$i]['orden'] = $GrupMenu->getOrden() ?? 0;
    $a_valores[$i]['menus'] = ($menusVisiblesPorGrupo)($id_gm);

}

$data['a_valores'] = $a_valores;

// envía una Response (`data` anidado para la app Kotlin; PostRequest espera enviar()+data string).
ContestarJson::enviarDataAnidado($error_txt, $data);
