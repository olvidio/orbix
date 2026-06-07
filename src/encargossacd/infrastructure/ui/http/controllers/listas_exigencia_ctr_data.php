<?php

use src\encargossacd\application\ListasExigenciaCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasExigenciaCtrData $useCase */
$useCase = DependencyResolver::get(ListasExigenciaCtrData::class);


$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);
$ctr_igl = (string)(filter_input(INPUT_POST, 'ctr_igl') ?? filter_input(INPUT_GET, 'ctr_igl') ?? '');

ContestarJson::enviar('', $useCase->execute($sf, $ctr_igl));
