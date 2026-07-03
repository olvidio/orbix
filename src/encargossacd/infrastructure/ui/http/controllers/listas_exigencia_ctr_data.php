<?php

use src\encargossacd\application\ListasExigenciaCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasExigenciaCtrData $useCase */
$useCase = DependencyResolver::get(ListasExigenciaCtrData::class);


$sf = (int)(FilterPostGet::post('sf') ?? FilterPostGet::get('sf') ?? 0);
$ctr_igl = (string)(FilterPostGet::post('ctr_igl') ?? FilterPostGet::get('ctr_igl') ?? '');

ContestarJson::enviar('', $useCase->execute($sf, $ctr_igl));
