<?php

use src\dbextern\application\TrasladarPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo_persona');
$id_nom_orbix = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_nom_orbix');

$jsondata = DependencyResolver::get(TrasladarPersonaUseCase::class)->trasladarA($id_nom_orbix, $tipo_persona, $dl);

$error_txt = !empty($jsondata['success']) ? '' : (string)($jsondata['mensaje'] ?? _("Error al trasladar"));
ContestarJson::enviar($error_txt, $jsondata);
