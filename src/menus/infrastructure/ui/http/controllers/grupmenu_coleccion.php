<?php

use src\menus\application\GrupMenuColeccionUseCase;
use src\menus\application\MenusVisiblesPorGrupoMenuUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var GrupMenuColeccionUseCase $grupMenusCollecion */
$grupMenusCollecion = DependencyResolver::get(GrupMenuColeccionUseCase::class);
$cGrupMenus = $grupMenusCollecion();

/** @var MenusVisiblesPorGrupoMenuUseCase $menusVisiblesPorGrupo */
$menusVisiblesPorGrupo = DependencyResolver::get(MenusVisiblesPorGrupoMenuUseCase::class);

$ambito = 'dl';
$oConfig = $_SESSION['oConfig'] ?? null;
if (is_object($oConfig) && method_exists($oConfig, 'getAmbito')) {
    $ambito = $oConfig->getAmbito();
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

ContestarJson::enviarDataAnidado($error_txt, $data);
