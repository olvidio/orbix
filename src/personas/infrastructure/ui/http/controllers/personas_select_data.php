<?php

/**
 * Endpoint JSON: datos crudos para la tabla `personas_select`.
 */

use src\personas\application\PersonasSelectData;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

try {
    $result = PersonasSelectData::build($_POST);
} catch (\Throwable $e) {
    if (RegionStgrAviso::esDlSinRegion($e) || RegionStgrAviso::esMensajeSuave($e->getMessage())) {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, $e);
        $result = [
            'tabla' => (string)($_POST['tabla'] ?? ''),
            'obj_pau' => '',
            'id_tabla' => '',
            'permiso' => 1,
            'sPrefs' => '',
            'total' => 0,
            'personas' => [],
            'aviso' => RegionStgrAviso::combinarAvisos(
                RegionStgrAviso::formatear($problemas),
                RegionStgrAviso::esMensajeSuave($e->getMessage()) && !RegionStgrAviso::esDlSinRegion($e)
                    ? (str_contains($e->getMessage() ?? '', _('persona no válida')) ? RegionStgrAviso::mensajePersonaNoValida() : $e->getMessage())
                    : '',
            ),
        ];
    } else {
        ContestarJson::enviar($e->getMessage());
        return;
    }
}

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
