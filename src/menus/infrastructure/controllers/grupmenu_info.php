<?php

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use web\ContestarJson;

$Qid_grupmenu = (string)filter_input(INPUT_POST, 'id_grupmenu');

$GrupMenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
$oGrupo = $GrupMenuRepository->findById($Qid_grupmenu);
$grupmenu = $oGrupo->getGrup_menu();
$orden = $oGrupo->getOrden();

$error_txt = '';
$data['grupmenu'] = $grupmenu;
$data['orden'] = $orden;

ContestarJson::enviar($error_txt, $data);