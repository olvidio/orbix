<?php

declare(strict_types=1);

namespace src\shared\domain\contracts;

/**
 * Calendario escolar (inicio y fin del curso de stgr y de crt) para {@see \src\shared\domain\Periodo}.
 */
interface CalendarioEscolarInterface
{
    /**
     * @return array<string, int> mes_fin_stgr, mes_fin_crt, dia_ini_stgr, mes_ini_stgr, dia_fin_stgr, dia_ini_crt,
     *                            mes_ini_crt, dia_fin_crt, any_final_est, any_final_crt
     */
    public function calendario(): array;
}
