<?php

declare(strict_types=1);

namespace src\misas\application\support;

use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Resuelve un rango de fechas [min, max] en formato `Y-m-d` a partir de un
 * identificador de periodo ("esta_semana", "este_mes", "proxima_semana",
 * "proximo_mes", ...) y, en caso de `otro`, los límites `empiezamin`/`empiezamax`
 * proporcionados por el usuario.
 *
 * Extraído para eliminar la duplicación previa en `VerPlanCtrData` y `VerPlanSacdData`.
 */
class PeriodoDateRange
{
    /**
     * @return array{0: string, 1: string} [Y-m-d, Y-m-d]
     */
    public static function resolve(string $periodo, string $empiezamin, string $empiezamax): array
    {
        switch ($periodo) {
            case 'esta_semana':
                $dia_week = (int)date('N');
                $dia_week--;
                if ($dia_week === 0) {
                    $dia_week = 6;
                }
                $empiezamin_dt = new DateTimeLocal(date('Y-m-d'));
                $di = new \DateInterval('P' . $dia_week . 'D');
                $di->invert = 1;
                $empiezamin_dt->add($di);
                $min = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $max = $empiezamax_dt->format('Y-m-d');

                return [$min, $max];
            case 'proxima_semana':
                $dia_week = (int)date('N');
                $empiezamin_dt = new DateTimeLocal(date('Y-m-d'));
                $empiezamin_dt->add(new \DateInterval('P' . (8 - $dia_week) . 'D'));
                $min = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $max = $empiezamax_dt->format('Y-m-d');

                return [$min, $max];
            case 'este_mes':
                $mes = (int)date('m');
                $anyo = (int)date('Y');
                $min = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $mes)))->format('Y-m-d');
                $siguiente = $mes + 1;
                if ($siguiente === 13) {
                    $siguiente = 1;
                    $anyo++;
                }
                $max = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $siguiente)))->format('Y-m-d');

                return [$min, $max];
            case 'proximo_mes':
                $proximo = (int)date('m') + 1;
                $anyo = (int)date('Y');
                if ($proximo === 13) {
                    $proximo = 1;
                    $anyo++;
                }
                $min = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $proximo)))->format('Y-m-d');
                $siguiente = $proximo + 1;
                if ($siguiente === 13) {
                    $siguiente = 1;
                    $anyo++;
                }
                $max = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $siguiente)))->format('Y-m-d');

                return [$min, $max];
            default:
                $oInicio = DateTimeLocal::createFromLocal($empiezamin);
                $oFin = DateTimeLocal::createFromLocal($empiezamax);
                if (!($oInicio instanceof DateTimeLocal) || !($oFin instanceof DateTimeLocal)) {
                    return [
                        str_replace('/', '-', $empiezamin),
                        str_replace('/', '-', $empiezamax),
                    ];
                }

                return [$oInicio->format('Y-m-d'), $oFin->format('Y-m-d')];
        }
    }
}
