<?php

namespace src\configuracion\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en
 * `$_SESSION['oConfig']`, para inyectar en `Periodo` del frontend.
 */
final class PeriodoCalendarioEscolarData
{
    public function __construct(
        private ObtenerConfigSnapshot $obtenerConfigSnapshot,
    ) {
    }

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
     *   any_final_est: int,
     *   any_final_crt: int
     * }
     */
    public function execute(): array
    {
        $sessionConfig = $_SESSION['oConfig'] ?? null;
        $oConfig = $sessionConfig instanceof ConfigSnapshot
            ? $sessionConfig
            : $this->obtenerConfigSnapshot->execute();

        return [
            'mes_fin_stgr' => $oConfig->getMesFinStgr(),
            'mes_fin_crt' => $oConfig->getMesFinCrt(),
            'dia_ini_stgr' => $oConfig->getDiaIniStgr(),
            'mes_ini_stgr' => $oConfig->getMesIniStgr(),
            'dia_fin_stgr' => $oConfig->getDiaFinStgr(),
            'dia_ini_crt' => $oConfig->getDiaIniCrt(),
            'mes_ini_crt' => $oConfig->getMesIniCrt(),
            'dia_fin_crt' => $oConfig->getDiaFinCrt(),
            'any_final_est' => $oConfig->any_final_curs('est'),
            'any_final_crt' => $oConfig->any_final_curs('crt'),
        ];
    }
}
