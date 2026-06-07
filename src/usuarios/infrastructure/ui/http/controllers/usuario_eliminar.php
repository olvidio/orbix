<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuarioEliminar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string_list;

$a_sel = input_string_list($_POST, 'sel');

/** @var usuarioEliminar $useCase */
$useCase = DependencyResolver::get(usuarioEliminar::class);
$result = $useCase->execute($a_sel);

ContestarJson::enviar($result['error'], $result['data']);
