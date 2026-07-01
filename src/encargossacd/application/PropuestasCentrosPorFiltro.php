<?php

namespace src\encargossacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;

/**
 * Centros activos filtrados por grupo (propuestas / listados por encargo).
 */
final class PropuestasCentrosPorFiltro
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @return list<CentroDl|CentroEllas>
     */
    public function execute(int $filtro_ctr, bool $todosEnDefault = false): array
    {
        return match ($filtro_ctr) {
            1 => $this->centroDlRepository->getCentros([
                'tipo_ctr' => '^a|n|s[^s]|of',
                'active' => 't',
                '_ordre' => 'nombre_ubi',
            ], ['tipo_ctr' => '~']) ?: [],
            2 => $this->centroEllasRepository->getCentros([
                'active' => 't',
                '_ordre' => 'nombre_ubi',
            ]) ?: [],
            3 => $this->centroDlRepository->getCentros([
                'tipo_ctr' => '^ss',
                'active' => 't',
                '_ordre' => 'nombre_ubi',
            ], ['tipo_ctr' => '~']) ?: [],
            4 => $this->centroDlRepository->getCentros([
                'tipo_ctr' => 'igl',
                'active' => 't',
                '_ordre' => 'nombre_ubi',
            ], ['tipo_ctr' => '~']) ?: [],
            5 => array_merge(
                $this->centroDlRepository->getCentros([
                    'tipo_ctr' => 'cgioc|oc|cgi',
                    'active' => 't',
                    '_ordre' => 'nombre_ubi',
                ], ['tipo_ctr' => '~']) ?: [],
                $this->centroEllasRepository->getCentros([
                    'tipo_ctr' => 'cgioc|oc|cgi',
                    'active' => 't',
                    '_ordre' => 'nombre_ubi',
                ], ['tipo_ctr' => '~']) ?: [],
            ),
            default => $todosEnDefault ? array_merge(
                $this->centroDlRepository->getCentros([
                    'active' => 't',
                    '_ordre' => 'tipo_ctr, nombre_ubi',
                ]) ?: [],
                $this->centroEllasRepository->getCentros([
                    'active' => 't',
                    '_ordre' => 'tipo_ctr, nombre_ubi',
                ]) ?: [],
            ) : [],
        };
    }
}
