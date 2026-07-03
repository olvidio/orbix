<?php

use src\encargossacd\application\CtrGetFichaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var CtrGetFichaData $useCase */
$useCase = DependencyResolver::get(CtrGetFichaData::class);


$id_ubi = (int)(FilterPostGet::post('id_ubi') ?? FilterPostGet::get('id_ubi') ?? 0);
$seleccion_sacd = (int)(FilterPostGet::post('seleccion_sacd') ?? FilterPostGet::get('seleccion_sacd') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_ubi, $seleccion_sacd));
