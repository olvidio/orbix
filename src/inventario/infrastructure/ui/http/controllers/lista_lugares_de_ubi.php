<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_ubi = input_int($_POST, 'id_ubi');
$error_txt = '';

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
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