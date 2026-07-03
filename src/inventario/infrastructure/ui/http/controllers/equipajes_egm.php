<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\ListaDocsGrupo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$Qid_equipaje = FuncTablasSupport::inputInt($_POST, 'id_equipaje');
$error_txt = '';

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
/** @var ListaDocsGrupo $listaDocsGrupo */
$listaDocsGrupo = DependencyResolver::get(ListaDocsGrupo::class);

$cEgm = $EgmRepository->getEgmes(['id_equipaje' => $Qid_equipaje, '_ordre' => 'id_grupo']);
$a_egm = [];
$i = 0;
foreach ($cEgm as $oEgm) {
    $i++;
    $id_grupo = $oEgm->getId_grupo();
    $a_egm[$i]['id_grupo'] = $id_grupo;
    $id_lugar = $oEgm->getId_lugar();
    $a_egm[$i]['id_lugar'] = $id_lugar;
    $texto = $oEgm->getTextoVo()?->value();
    $a_egm[$i]['texto'] = $texto;

    $oLugar = $LugarRepository->findById((int) $id_lugar);
    $nom_lugar = $oLugar !== null ? $oLugar->getNom_lugar() : '';
    $a_egm[$i]['nom_lugar'] = $nom_lugar;

    if ($id_lugar === null || $id_grupo === null) {
        continue;
    }
    $datos = $listaDocsGrupo->listaDocsGrupo($Qid_equipaje, $id_lugar, $id_grupo);

    $a_egm[$i]['a_valores'] = $datos['a_valores'];
    $a_egm[$i]['id_item_egm'] = $datos['id_item_egm'];
}

$data = [
    'a_egm' => $a_egm,
];

ContestarJson::enviar($error_txt, $data);
