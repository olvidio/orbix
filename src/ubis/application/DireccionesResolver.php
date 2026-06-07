<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;

final class DireccionesResolver
{
    public function __construct(
        private DireccionCentroDlRepositoryInterface $direccionCentroDlRepository,
        private DireccionCentroExRepositoryInterface $direccionCentroExRepository,
        private DireccionCasaDlRepositoryInterface $direccionCasaDlRepository,
        private DireccionCasaExRepositoryInterface $direccionCasaExRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroExRepositoryInterface $centroExRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
        private CasaExRepositoryInterface $casaExRepository,
    ) {
    }

    public function direccionRepo(string $obj_dir): DireccionCentroDlRepositoryInterface|DireccionCentroExRepositoryInterface|DireccionCasaDlRepositoryInterface|DireccionCasaExRepositoryInterface
    {
        return match ($obj_dir) {
            'DireccionCentroDl' => $this->direccionCentroDlRepository,
            'DireccionCentroEx' => $this->direccionCentroExRepository,
            'DireccionCdcDl' => $this->direccionCasaDlRepository,
            'DireccionCdcEx' => $this->direccionCasaExRepository,
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }

    public function ubiRepo(string $obj_dir): CentroDlRepositoryInterface|CentroExRepositoryInterface|CasaDlRepositoryInterface|CasaExRepositoryInterface
    {
        return match ($obj_dir) {
            'DireccionCentroDl' => $this->centroDlRepository,
            'DireccionCentroEx' => $this->centroExRepository,
            'DireccionCdcDl' => $this->casaDlRepository,
            'DireccionCdcEx' => $this->casaExRepository,
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }
}
