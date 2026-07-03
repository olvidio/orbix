<?php

use src\encargossacd\application\PropuestasAjaxDispatch;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$que = (string) (\src\shared\domain\helpers\FilterPostGet::post('que') ?? \src\shared\domain\helpers\FilterPostGet::get('que') ?? '');

/** @var PropuestasAjaxDispatch $useCase */
$useCase = DependencyResolver::get(PropuestasAjaxDispatch::class);
ContestarJson::enviar('', $useCase->execute($que));
