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
    public static function resolve(string $periodo, string $empiezamin, string $empiezamax, ?\DateTimeInterface $hoy = null): array
    {
        $hoyIso = ($hoy ?? new \DateTimeImmutable('today'))->format('Y-m-d');
        switch ($periodo) {
            case 'esta_semana':
                // Días desde el lunes de esta semana (0 si hoy es lunes; antes, en lunes, retrocedía 6 días).
                $dia_week = (int)(new \DateTimeImmutable($hoyIso))->format('N') - 1;
                $empiezamin_dt = new DateTimeLocal($hoyIso);
                $di = new \DateInterval('P' . $dia_week . 'D');
                $di->invert = 1;
                $empiezamin_dt->add($di);
                $min = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $max = $empiezamax_dt->format('Y-m-d');

                return [$min, $max];
            case 'proxima_semana':
                $dia_week = (int)(new \DateTimeImmutable($hoyIso))->format('N');
                $empiezamin_dt = new DateTimeLocal($hoyIso);
                $empiezamin_dt->add(new \DateInterval('P' . (8 - $dia_week) . 'D'));
                $min = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $max = $empiezamax_dt->format('Y-m-d');

                return [$min, $max];
            case 'este_mes':
                $mes = (int)substr($hoyIso, 5, 2);
                $anyo = (int)substr($hoyIso, 0, 4);
                $min = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $mes)))->format('Y-m-d');
                $siguiente = $mes + 1;
                if ($siguiente === 13) {
                    $siguiente = 1;
                    $anyo++;
                }
                $max = (new DateTimeLocal(sprintf('%04d-%02d-01', $anyo, $siguiente)))->format('Y-m-d');

                return [$min, $max];
            case 'proximo_mes':
                $proximo = (int)substr($hoyIso, 5, 2) + 1;
                $anyo = (int)substr($hoyIso, 0, 4);
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
