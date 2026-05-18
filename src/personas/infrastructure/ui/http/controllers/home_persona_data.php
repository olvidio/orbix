<?php

/**
 * Endpoint JSON: datos para la pantalla `home_persona.phtml`.
 */

use src\personas\application\HomePersonaData;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

$result = HomePersonaData::build($_POST);

if (!empty($result['error'])) {
    if (RegionStgrAviso::esMensajeSuave((string)$result['error'])) {
        $result['aviso'] = RegionStgrAviso::combinarAvisos(
            (string)($result['aviso'] ?? ''),
            str_contains((string)$result['error'], _('persona no válida'))
                ? RegionStgrAviso::mensajePersonaNoValida()
                : (string)$result['error'],
        );
        unset($result['error']);
    } else {
        ContestarJson::enviar((string)$result['error']);
        return;
    }
}

ContestarJson::enviar('', $result);
