<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `procesos_select`.
 *
 * Devuelve las opciones del desplegable de tipo de proceso para que la vista
 * frontend monte el `frontend\shared\web\Desplegable` y los `web\Hash` correspondientes.
 */
class ProcesosSelectData
{
    public static function execute(): array
    {
        $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);

        return [
            'a_tipos_proceso' => $ProcesoTipoRepository->getArrayProcesoTipos(),
        ];
    }
}
