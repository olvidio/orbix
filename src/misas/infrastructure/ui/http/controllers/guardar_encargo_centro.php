<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\GuardarEncargoCentro;
use src\shared\web\ContestarJson;

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');
$Qid_enc = (int)filter_input(INPUT_POST, 'id_enc', FILTER_VALIDATE_INT);
$Qid_ctr = (int)filter_input(INPUT_POST, 'id_ctr', FILTER_VALIDATE_INT);

/** @var GuardarEncargoCentro $useCase */
$useCase = DependencyResolver::get(GuardarEncargoCentro::class);
$result = $useCase->execute($Qid_item, $Qid_enc, $Qid_ctr);

ContestarJson::enviar($result, [
    'id_item' => $Qid_item,
    'id_enc' => $Qid_enc,
    'id_ctr' => $Qid_ctr,
]);
