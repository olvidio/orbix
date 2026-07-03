<?php

use src\encargossacd\application\ListasComCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var ListasComCtrData $useCase */
$useCase = DependencyResolver::get(ListasComCtrData::class);


$sfsv = (string)(FilterPostGet::post('sfsv') ?? FilterPostGet::get('sfsv') ?? '');

ContestarJson::enviar('', $useCase->execute($sfsv));
