<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint JSON: datos crudos para la tabla `personas_select`.
 */

use src\personas\application\PersonasSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;

/** @var PersonasSelectData $useCase */
$useCase = DependencyResolver::get(PersonasSelectData::class);

try {
    $result = $useCase->execute($_POST);
} catch (\Throwable $e) {
    $msg = $e->getMessage();
    if (RegionStgrAviso::esDlSinRegion($e) || RegionStgrAviso::esMensajeSuave($msg)) {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, $e);
        $result = [
            'tabla' => FuncTablasSupport::inputString($_POST, 'tabla'),
            'obj_pau' => '',
            'id_tabla' => '',
            'permiso' => 1,
            'sPrefs' => '',
            'total' => 0,
            'personas' => [],
            'aviso' => RegionStgrAviso::combinarAvisos(
                RegionStgrAviso::formatear($problemas),
                RegionStgrAviso::esMensajeSuave($msg) && !RegionStgrAviso::esDlSinRegion($e)
                    ? (str_contains($msg, _('persona no válida')) ? RegionStgrAviso::mensajePersonaNoValida() : $msg)
                    : '',
            ),
        ];
    } else {
        ContestarJson::enviar($msg);
        return;
    }
}

$errorVal = $result['error'] ?? '';
if (is_string($errorVal) && $errorVal !== '') {
    if (RegionStgrAviso::esMensajeSuave($errorVal)) {
        $avisoPrev = $result['aviso'] ?? '';
        $result['aviso'] = RegionStgrAviso::combinarAvisos(
            is_string($avisoPrev) ? $avisoPrev : '',
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
