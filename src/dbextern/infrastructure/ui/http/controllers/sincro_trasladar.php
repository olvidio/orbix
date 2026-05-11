<?php

use src\shared\web\ContestarJson;
use src\dbextern\application\TrasladarPersonaUseCase;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$id_nom_orbix = (string)filter_input(INPUT_POST, 'id_nom_orbix');

$delegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
$useCase = new TrasladarPersonaUseCase($delegacionRepository);
$jsondata = $useCase->trasladar($id_nom_orbix, $tipo_persona, $dl);

$error_txt = ($jsondata['success'] ?? true) ? '' : ($jsondata['mensaje'] ?? _("Error al trasladar"));
ContestarJson::enviar($error_txt, $jsondata);
