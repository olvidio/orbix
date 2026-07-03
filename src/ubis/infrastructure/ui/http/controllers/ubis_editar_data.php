<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEditarOpcionesData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qobj_pau = FuncTablasSupport::inputString($_POST, 'obj_pau');
$Qtipo_ubi = FuncTablasSupport::inputString($_POST, 'tipo_ubi');
$Qdl = FuncTablasSupport::inputString($_POST, 'dl');
$Qregion = FuncTablasSupport::inputString($_POST, 'region');

ContestarJson::enviar('', DependencyResolver::get(UbisEditarOpcionesData::class)->execute($Qobj_pau, $Qtipo_ubi, $Qdl, $Qregion));
