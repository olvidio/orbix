<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEditarOpcionesData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$Qobj_pau = input_string($_POST, 'obj_pau');
$Qtipo_ubi = input_string($_POST, 'tipo_ubi');
$Qdl = input_string($_POST, 'dl');
$Qregion = input_string($_POST, 'region');

ContestarJson::enviar('', DependencyResolver::get(UbisEditarOpcionesData::class)->execute($Qobj_pau, $Qtipo_ubi, $Qdl, $Qregion));
