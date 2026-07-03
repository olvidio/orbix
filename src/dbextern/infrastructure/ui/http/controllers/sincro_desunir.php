<?php

use src\dbextern\application\DesunirPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$id_nom_listas = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom_listas');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo_persona');

$error_txt = DependencyResolver::get(DesunirPersonaUseCase::class)($id_nom_listas, $tipo_persona);

ContestarJson::enviar($error_txt, 'ok');
