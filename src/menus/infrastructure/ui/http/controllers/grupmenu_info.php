<?php

use src\shared\infrastructure\DependencyResolver;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_grupmenu = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_grupmenu');

/** @var GrupMenuRepositoryInterface $GrupMenuRepository */
$GrupMenuRepository = DependencyResolver::get(GrupMenuRepositoryInterface::class);
$oGrupo = $GrupMenuRepository->findById($Qid_grupmenu);
if ($oGrupo === null) {
    ContestarJson::enviar(_("No encuentro el grupmenu"), []);
    return;
}
$grupmenu = $oGrupo->getGrup_menu();
$orden = $oGrupo->getOrden() ?? 0;

$error_txt = '';
$data['grupmenu'] = $grupmenu;
$data['orden'] = $orden;

ContestarJson::enviar($error_txt, $data);
