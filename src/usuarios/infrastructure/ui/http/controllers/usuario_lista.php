<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosLista;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$Qusername = input_string($_POST, 'username');

/** @var usuariosLista $useCase */
$useCase = DependencyResolver::get(usuariosLista::class);
$jsondata = $useCase->execute($Qusername);

ContestarJson::send($jsondata);
