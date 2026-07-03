<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoTablaData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qobj_pau = FuncTablasSupport::inputString($_POST, 'obj_pau');
$Qid_ubi = FuncTablasSupport::inputInt($_POST, 'id_ubi');

ContestarJson::enviar('', DependencyResolver::get(TelecoTablaData::class)->execute($Qobj_pau, $Qid_ubi));
