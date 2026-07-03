<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\usuariosLista;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qusername = FuncTablasSupport::inputString($_POST, 'username');

/** @var usuariosLista $useCase */
$useCase = DependencyResolver::get(usuariosLista::class);
$jsondata = $useCase->execute($Qusername);

ContestarJson::send($jsondata);
