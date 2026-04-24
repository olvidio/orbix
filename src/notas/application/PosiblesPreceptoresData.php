<?php

namespace src\notas\application;

use src\profesores\domain\services\ProfesorStgrService;

/**
 * Devuelve el diccionario `[id_nom => apellidos_nombre]` de posibles
 * preceptores (profesores del plan STGR de la dl actual). Pensado para
 * poblar el desplegable `id_preceptor` en `form_notas_de_una_persona.phtml` y
 * `form_1303.phtml`.
 */
final class PosiblesPreceptoresData
{
    public static function execute(): array
    {
        $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
        return $ProfesorStgrService->getArrayProfesoresDl();
    }
}
