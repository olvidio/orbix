<?php

use function src\shared\domain\helpers\is_true;

use src\procesos\application\ActividadQueFasesCuadro;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
$QselectedCsv = (string)filter_input(INPUT_POST, 'selected');

$dl_propia = (bool)is_true($Qdl_propia);
$selected = $QselectedCsv === ''
    ? []
    : array_values(array_filter(array_map('intval', explode(',', $QselectedCsv))));

/** @var ActividadQueFasesCuadro $useCase */
$useCase = DependencyResolver::get(ActividadQueFasesCuadro::class);
ContestarJson::enviar('', $useCase->ejecutar($Qid_tipo_activ, $dl_propia, $selected));
