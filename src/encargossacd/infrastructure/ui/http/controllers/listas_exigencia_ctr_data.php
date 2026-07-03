<?php

use src\encargossacd\application\ListasExigenciaCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasExigenciaCtrData $useCase */
$useCase = DependencyResolver::get(ListasExigenciaCtrData::class);


$sf = (int)(\src\shared\domain\helpers\FilterPostGet::post('sf') ?? \src\shared\domain\helpers\FilterPostGet::get('sf') ?? 0);
$ctr_igl = (string)(\src\shared\domain\helpers\FilterPostGet::post('ctr_igl') ?? \src\shared\domain\helpers\FilterPostGet::get('ctr_igl') ?? '');

ContestarJson::enviar('', $useCase->execute($sf, $ctr_igl));
