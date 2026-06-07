<?php

use src\dbextern\application\TrasladarPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$dl = input_string($_POST, 'dl');
$tipo_persona = input_string($_POST, 'tipo_persona');
$id_nom_orbix = input_int($_POST, 'id_nom_orbix');

$jsondata = DependencyResolver::get(TrasladarPersonaUseCase::class)->trasladarA($id_nom_orbix, $tipo_persona, $dl);

$error_txt = !empty($jsondata['success']) ? '' : (string)($jsondata['mensaje'] ?? _("Error al trasladar"));
ContestarJson::enviar($error_txt, $jsondata);
