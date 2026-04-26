<?php

use src\inventario\domain\contracts\LugarRepositoryInterface;
use frontend\shared\web\ContestarJson;

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$error_txt = '';

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$cLugares = $LugarRepository->getLugares(['id_ubi' => $Qid_ubi, '_ordre' => 'nom_lugar']);
$a = 0;
$a_valores = [];
foreach ($cLugares as $oLugar) {
    $a++;
    $id_lugar = $oLugar->getId_lugar();
    $a_valores[] = ['value' => $id_lugar, 'text' => $oLugar->getNom_lugar()];
}

// envía una Response
ContestarJson::enviar($error_txt, $a_valores);