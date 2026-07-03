<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdLista;
$input = ['id_zona' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona')];

/** @var ZonaSacdLista $useCase */
$useCase = DependencyResolver::get(ZonaSacdLista::class);
ContestarJson::enviar('', $useCase->execute($input['id_zona']));
