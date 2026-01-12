<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\ListaDocsGrupo;
use web\ContestarJson;

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');
$error_txt = '';

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
$cEgm = $EgmRepository->getEgmes(['id_equipaje' => $Qid_equipaje, '_ordre' => 'id_grupo']);
$id_grupo = 0;
$html_g = '';
$a_egm = [];
$i = 0;
foreach ($cEgm as $oEgm) {
    $i++;
    $id_grupo = $oEgm->getId_grupo();
    $a_egm[$i]['id_grupo'] = $id_grupo;
    $id_lugar = $oEgm->getId_lugar();
    $a_egm[$i]['id_lugar'] = $id_lugar;
    $texto = $oEgm->getTextoVo()->value();
    $a_egm[$i]['texto'] = $texto;

    $oLugar = $LugarRepository->findById($id_lugar);
    $nom_lugar = $oLugar->getNom_lugar();
    $a_egm[$i]['nom_lugar'] = $nom_lugar;
    // lista_docs_grupo($Qid_equipaje, $id_lugar, $id_grupo);
    $datos = ListaDocsGrupo::lista_docs_grupo($Qid_equipaje, $id_lugar, $id_grupo);

    $a_egm[$i]['a_valores'] = $datos['a_valores'];
    $a_egm[$i]['id_item_egm'] = $datos['id_item_egm'];
}

$data = [
    'a_egm' => $a_egm,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);