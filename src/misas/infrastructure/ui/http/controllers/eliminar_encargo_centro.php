<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\EliminarEncargoCentro;
use src\shared\web\ContestarJson;

$Qid_item = (string)filter_input(INPUT_POST, 'id_item');

/** @var EliminarEncargoCentro $useCase */
$useCase = DependencyResolver::get(EliminarEncargoCentro::class);
$result = $useCase->execute($Qid_item);

ContestarJson::enviar($result, ['id_item' => $Qid_item]);
