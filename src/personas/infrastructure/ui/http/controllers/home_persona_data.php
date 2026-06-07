<?php

/**
 * Endpoint JSON: datos para la pantalla `home_persona.phtml`.
 */

use src\personas\application\HomePersonaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

/** @var HomePersonaData $useCase */
$useCase = DependencyResolver::get(HomePersonaData::class);
$result = $useCase->execute($_POST);

$errorVal = $result['error'] ?? '';
if (is_string($errorVal) && $errorVal !== '') {
    if (RegionStgrAviso::esMensajeSuave($errorVal)) {
        $result['aviso'] = RegionStgrAviso::combinarAvisos(
            is_string($result['aviso'] ?? null) ? $result['aviso'] : '',
            str_contains($errorVal, _('persona no válida'))
                ? RegionStgrAviso::mensajePersonaNoValida()
                : $errorVal,
        );
        unset($result['error']);
    } else {
        ContestarJson::enviar($errorVal);
        return;
    }
}

ContestarJson::enviar('', $result);
