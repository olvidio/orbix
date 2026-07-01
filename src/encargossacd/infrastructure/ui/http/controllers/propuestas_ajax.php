<?php

use src\encargossacd\application\PropuestasAjaxDispatch;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$que = (string) (filter_post('que') ?? filter_get('que') ?? '');

/** @var PropuestasAjaxDispatch $useCase */
$useCase = DependencyResolver::get(PropuestasAjaxDispatch::class);
ContestarJson::enviar('', $useCase->execute($que));
