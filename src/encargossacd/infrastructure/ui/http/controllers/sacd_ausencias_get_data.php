<?php

use src\encargossacd\application\SacdAusenciasGetData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var SacdAusenciasGetData $useCase */
$useCase = DependencyResolver::get(SacdAusenciasGetData::class);


$id_nom = (int)(FilterPostGet::post('id_nom') ?? FilterPostGet::get('id_nom') ?? 0);
$historial = (int)(FilterPostGet::post('historial') ?? FilterPostGet::get('historial') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_nom, $historial));
