<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuarioEliminar;
use src\shared\web\ContestarJson;

$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');

/** @var usuarioEliminar $useCase */
$useCase = DependencyResolver::get(usuarioEliminar::class);
$result = $useCase->execute($a_sel);

ContestarJson::enviar($result['error'], $result['data']);
