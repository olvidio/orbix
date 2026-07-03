<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEditarOpcionesData;
use src\shared\web\ContestarJson;

$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau');
$Qtipo_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo_ubi');
$Qdl = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl');
$Qregion = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'region');

ContestarJson::enviar('', DependencyResolver::get(UbisEditarOpcionesData::class)->execute($Qobj_pau, $Qtipo_ubi, $Qdl, $Qregion));
