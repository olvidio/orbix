<?php

use src\encargossacd\application\CtrFichaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CtrFichaData $useCase */
$useCase = DependencyResolver::get(CtrFichaData::class);


$id_ubi = (int)(filter_post('id_ubi') ?? filter_get('id_ubi') ?? 0);
$filtro_ctr = (int)(filter_post('filtro_ctr') ?? filter_get('filtro_ctr') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_ubi, $filtro_ctr));
