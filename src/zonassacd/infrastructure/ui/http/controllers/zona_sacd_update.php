<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdUpdate;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'id_zona' => input_string($_POST, 'id_zona'),
    'id_zona_new' => input_string($_POST, 'id_zona_new'),
    'acumular' => input_int($_POST, 'acumular'),
    'sel' => input_string_list($_POST, 'sel'),
];

/** @var ZonaSacdUpdate $useCase */
$useCase = DependencyResolver::get(ZonaSacdUpdate::class);
ContestarJson::enviar('', $useCase->execute(
    $input['id_zona'],
    $input['id_zona_new'],
    $input['acumular'],
    $input['sel'],
));
