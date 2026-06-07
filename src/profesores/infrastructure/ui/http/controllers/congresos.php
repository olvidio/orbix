<?php

use src\profesores\application\CongresosLista;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CongresosLista $useCase */
$useCase = DependencyResolver::get(CongresosLista::class);
ContestarJson::enviar('', $useCase->getTablaData());
