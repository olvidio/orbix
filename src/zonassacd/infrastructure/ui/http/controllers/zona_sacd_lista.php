<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdLista;
use src\shared\domain\helpers\FuncTablasSupport;
$input = ['id_zona' => FuncTablasSupport::inputString($_POST, 'id_zona')];

/** @var ZonaSacdLista $useCase */
$useCase = DependencyResolver::get(ZonaSacdLista::class);
ContestarJson::enviar('', $useCase->execute($input['id_zona']));
