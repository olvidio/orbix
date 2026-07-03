<?php

use src\encargossacd\application\CtrFichaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var CtrFichaData $useCase */
$useCase = DependencyResolver::get(CtrFichaData::class);


$id_ubi = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_ubi') ?? \src\shared\domain\helpers\FilterPostGet::get('id_ubi') ?? 0);
$filtro_ctr = (int)(\src\shared\domain\helpers\FilterPostGet::post('filtro_ctr') ?? \src\shared\domain\helpers\FilterPostGet::get('filtro_ctr') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_ubi, $filtro_ctr));
