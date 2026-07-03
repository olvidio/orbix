<?php

use src\procesos\application\ActividadQueFasesCuadro;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_tipo_activ = (string)FilterPostGet::post('id_tipo_activ');
$Qdl_propia = (string)FilterPostGet::post('dl_propia');
$QselectedCsv = (string)FilterPostGet::post('selected');

$dl_propia = (bool)FuncTablasSupport::isTrue($Qdl_propia);
$selected = $QselectedCsv === ''
    ? []
    : array_values(array_filter(array_map('intval', explode(',', $QselectedCsv))));

/** @var ActividadQueFasesCuadro $useCase */
$useCase = DependencyResolver::get(ActividadQueFasesCuadro::class);
ContestarJson::enviar('', $useCase->ejecutar($Qid_tipo_activ, $dl_propia, $selected));
