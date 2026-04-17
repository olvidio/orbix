<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Datos comunes para los formularios de centro dl (labor / num / plazas).
 *
 * Los tres formularios muestran sobre un mismo centro un subconjunto de campos
 * distinto según el modo indicado.
 */
final class CentrosFormData
{
    public const MODO_LABOR = 'labor';
    public const MODO_NUM = 'num';
    public const MODO_PLAZAS = 'plazas';

    public static function execute(int $id_ubi, string $modo): array
    {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($id_ubi);

        $base = [
            'id_ubi' => $id_ubi,
            'nombre_ubi' => $oCentro?->getNombre_ubi() ?? '',
        ];

        return match ($modo) {
            self::MODO_LABOR => $base + [
                'tipo_ctr' => $oCentro?->getTipo_ctr() ?? '',
                'tipo_labor' => $oCentro?->getTipo_labor() ?? 0,
            ],
            self::MODO_NUM => $base + [
                'n_buzon' => $oCentro?->getN_buzon() ?? '',
                'num_pi' => $oCentro?->getNum_pi() ?? '',
                'num_cartas' => $oCentro?->getNum_cartas() ?? '',
            ],
            self::MODO_PLAZAS => $base + [
                'num_habit_indiv' => $oCentro?->getNum_habit_indiv() ?? '',
                'plazas' => $oCentro?->getPlazas() ?? '',
                'sede' => $oCentro?->isSede() ?? false,
            ],
            default => throw new \InvalidArgumentException("Modo desconocido: $modo"),
        };
    }
}
