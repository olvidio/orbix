<?php

declare(strict_types=1);

namespace Tests\support;

use src\shared\domain\contracts\CalendarioEscolarInterface;

/** Calendario escolar fijo para los tests que calculan un `Periodo` (curso de stgr de octubre a junio). */
final class CalendarioEscolarFijo implements CalendarioEscolarInterface
{
    public const CALENDARIO = [
        'mes_fin_stgr' => 6, 'mes_fin_crt' => 8,
        'dia_ini_stgr' => 1, 'mes_ini_stgr' => 10, 'dia_fin_stgr' => 30,
        'dia_ini_crt' => 1, 'mes_ini_crt' => 9, 'dia_fin_crt' => 31,
        'any_final_est' => 2027, 'any_final_crt' => 2027,
    ];

    public function calendario(): array
    {
        return self::CALENDARIO;
    }
}
