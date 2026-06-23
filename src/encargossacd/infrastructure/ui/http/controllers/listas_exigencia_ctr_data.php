<?php

use src\encargossacd\application\ListasExigenciaCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasExigenciaCtrData $useCase */
$useCase = DependencyResolver::get(ListasExigenciaCtrData::class);


$sf = (int)(filter_post('sf') ?? filter_get('sf') ?? 0);
$ctr_igl = (string)(filter_post('ctr_igl') ?? filter_get('ctr_igl') ?? '');

ContestarJson::enviar('', $useCase->execute($sf, $ctr_igl));
