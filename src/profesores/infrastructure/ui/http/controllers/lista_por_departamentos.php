<?php

use src\profesores\application\ListaPorDepartamentos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var ListaPorDepartamentos $useCase */
$useCase = DependencyResolver::get(ListaPorDepartamentos::class);
$data = $useCase->getData(FuncTablasSupport::inputStringList($_POST, 'dl'), FuncTablasSupport::inputInt($_POST, 'filtro'));
ContestarJson::enviar('', $data);
