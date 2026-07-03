<?php

use src\dbextern\application\BajaPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo_persona');
$id_nom_orbix = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom_orbix');

$error_txt = DependencyResolver::get(BajaPersonaUseCase::class)($id_nom_orbix, $tipo_persona, $dl);

ContestarJson::enviar($error_txt, 'ok');
