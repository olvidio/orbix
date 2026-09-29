<?php

namespace src\actividadessacd\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\security\HashB;

/**
 * Emite la cápsula HashB de `sacd_asignar_auto` atada a la fecha de inicio
 * de curso des (2 de septiembre del año final de curso). El frontend no
 * elige esa fecha: viaja solo dentro del ctx.
 */
final class SacdAsignarAutoFormData
{
    /**
     * @return array{f_ini_iso: string, ctx_asignar_auto: string}
     */
    public function execute(): array
    {
        $f_ini_iso = $this->inicioCursoDesIso();

        return [
            'f_ini_iso' => $f_ini_iso,
            'ctx_asignar_auto' => HashB::sign('sacd_asignar_auto', [
                'f_ini_iso' => $f_ini_iso,
            ]),
        ];
    }

    private function inicioCursoDesIso(): string
    {
        $oConfig = $_SESSION['oConfig'] ?? null;
        $any = 0;
        if ($oConfig instanceof ConfigSnapshot) {
            $any = $oConfig->any_final_curs('est');
        }
        if ($any <= 0) {
            $any = (int) date('Y');
        }

        return sprintf('%04d-09-02', $any);
    }
}
