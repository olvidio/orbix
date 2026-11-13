<?php

declare(strict_types=1);

namespace src\inventario\infrastructure\ui\http;

use frontend\shared\web\Periodo;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Rango de fechas (ISO) del formulario de periodo de «hacer equipajes»: `periodo` + `year`, o
 * `empiezamin` / `empiezamax` en formato local si el periodo es «otro» (o no viene).
 *
 * Antes, con «otro» las fechas del formulario iban tal cual a la consulta: PostgreSQL rechazaba
 * «31/12/2027» y leía «1/2/2027» como el 2 de enero.
 */
final class RangoPeriodoRequest
{
    /**
     * @param array<mixed> $post
     * @return array{inicio: string, fin: string}|string el rango, o el texto de error
     */
    public static function resolver(array $post): array|string
    {
        $periodo = FuncTablasSupport::inputString($post, 'periodo');
        $year = FuncTablasSupport::inputInt($post, 'year');
        $oPeriodo = new Periodo();
        $oPeriodo->setAny($year === 0 ? (int) date('Y') + 1 : $year);
        if ($periodo === '' || $periodo === 'otro') {
            $min = FuncTablasSupport::inputString($post, 'inicio');
            $max = FuncTablasSupport::inputString($post, 'fin');
            $min = $min !== '' ? $min : FuncTablasSupport::inputString($post, 'empiezamin');
            $max = $max !== '' ? $max : FuncTablasSupport::inputString($post, 'empiezamax');
            if ($min === '' || $max === '') {
                return sprintf(_("Faltan parámetros: %s"), 'periodo / empiezamin / empiezamax');
            }
            $oPeriodo->setEmpiezaMin($min);
            $oPeriodo->setEmpiezaMax($max);
            $periodo = 'otro';
        }
        $oPeriodo->setPeriodo($periodo);
        $inicio = (string) $oPeriodo->getF_ini_iso();
        $fin = (string) $oPeriodo->getF_fin_iso();
        if ($inicio === '' || $fin === '') {
            return sprintf(_("Fecha no válida: %s"), trim(($min ?? '') . ' - ' . ($max ?? '')));
        }

        return ['inicio' => $oPeriodo->getF_ini()->getIso(), 'fin' => $oPeriodo->getF_fin()->getIso()];
    }
}
