<?php

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\inventario\domain\criteria\LugarField as LugarF;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi');
$error_txt = '';

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
$cLugares = $LugarRepository->matching(
    Criteria::create()->where(Filter::eq(LugarF::IdUbi, $Qid_ubi))->orderBy(LugarF::NomLugar)
);
$a = 0;
$a_valores = [];
foreach ($cLugares as $oLugar) {
    $a++;
    $id_lugar = $oLugar->getId_lugar();
    $a_valores[] = ['value' => $id_lugar, 'text' => $oLugar->getNom_lugar()];
}

// envía una Response
ContestarJson::enviar($error_txt, $a_valores);