<?php

use src\encargossacd\application\ListasComCtrData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListasComCtrData $useCase */
$useCase = DependencyResolver::get(ListasComCtrData::class);


$sfsv = (string)(filter_input(INPUT_POST, 'sfsv') ?? filter_input(INPUT_GET, 'sfsv') ?? '');

ContestarJson::enviar('', $useCase->execute($sfsv));
