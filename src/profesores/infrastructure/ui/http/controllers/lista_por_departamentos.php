<?php

use src\profesores\application\ListaPorDepartamentos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaPorDepartamentos $useCase */
$useCase = DependencyResolver::get(ListaPorDepartamentos::class);
$data = $useCase->getData(\src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'dl'), \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'filtro'));
ContestarJson::enviar('', $data);
