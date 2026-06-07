<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\CuadrosLaborBits;

/**
 * Datos comunes para los formularios de centro dl (labor / num / plazas).
 *
 * Los tres formularios muestran sobre un mismo centro un subconjunto de campos
 * distinto según el modo indicado.
 */
final class CentrosFormData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    public const MODO_LABOR = 'labor';
    public const MODO_NUM = 'num';
    public const MODO_PLAZAS = 'plazas';
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi, string $modo): array
    {
        $CentroDlRepository = $this->centroDlRepository;
        $oCentro = $CentroDlRepository->findById($id_ubi);

        $base = [
            'id_ubi' => $id_ubi,
            'nombre_ubi' => $oCentro?->getNombre_ubi() ?? '',
        ];

        return match ($modo) {
            self::MODO_LABOR => $base + self::payloadLabor((int)($oCentro?->getTipo_labor() ?? 0), $oCentro?->getTipo_ctr() ?? ''),
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

    /**
     * @return array{
     *     tipo_ctr: string,
     *     tipo_labor: int,
     *     tipo_labor_bit_map: array<string, int>
     * }
     */
    private static function payloadLabor(int $tipo_labor, string $tipo_ctr): array
    {
        return [
            'tipo_ctr' => $tipo_ctr,
            'tipo_labor' => $tipo_labor,
            'tipo_labor_bit_map' => CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv()),
        ];
    }
}
