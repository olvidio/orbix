<?php

namespace src\configuracion\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en
 * `$_SESSION['oConfig']`, para inyectar en `Periodo` del frontend.
 */
final class PeriodoCalendarioEscolarData
{
    /**
     * @return array{
     *   mes_fin_stgr: int,
     *   mes_fin_crt: int,
     *   dia_ini_stgr: int,
     *   mes_ini_stgr: int,
     *   dia_fin_stgr: int,
     *   dia_ini_crt: int,
     *   mes_ini_crt: int,
     *   dia_fin_crt: int,
     *   mes_fin_crt: int,
     *   any_final_est: int,
     *   any_final_crt: int
     * }
     */
    public static function execute(): array
    {
        $oConfig = $_SESSION['oConfig'] ?? self::buildFallbackSnapshot();

        return [
            'mes_fin_stgr' => $oConfig->getMesFinStgr(),
            'mes_fin_crt' => $oConfig->getMesFinCrt(),
            'dia_ini_stgr' => $oConfig->getDiaIniStgr(),
            'mes_ini_stgr' => $oConfig->getMesIniStgr(),
            'dia_fin_stgr' => $oConfig->getDiaFinStgr(),
            'dia_ini_crt' => $oConfig->getDiaIniCrt(),
            'mes_ini_crt' => $oConfig->getMesIniCrt(),
            'dia_fin_crt' => $oConfig->getDiaFinCrt(),
            'mes_fin_crt' => $oConfig->getMesFinCrt(),
            'any_final_est' => $oConfig->any_final_curs('est'),
            'any_final_crt' => $oConfig->any_final_curs('crt'),
        ];
    }

    /**
     * Fallback para contextos donde la sesión no se ha inicializado con
     * `global_object.inc` (p. ej. scripts de mantenimiento). Resuelve el
     * snapshot por el contenedor para evitar re-implementar la carga.
     */
    private static function buildFallbackSnapshot(): ConfigSnapshot
    {
        return $GLOBALS['container']->get(ObtenerConfigSnapshot::class)->execute();
    }
}
