<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

final class DireccionesResolver
{
    public function __construct(
        private DireccionCentroRepositoryInterface $direccionCentroRepository,
        private DireccionCentroDlRepositoryInterface $direccionCentroDlRepository,
        private DireccionCentroExRepositoryInterface $direccionCentroExRepository,
        private DireccionCasaRepositoryInterface $direccionCasaRepository,
        private DireccionCasaDlRepositoryInterface $direccionCasaDlRepository,
        private DireccionCasaExRepositoryInterface $direccionCasaExRepository,
        private CentroRepositoryInterface $centroRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroExRepositoryInterface $centroExRepository,
        private CasaRepositoryInterface $casaRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
        private CasaExRepositoryInterface $casaExRepository,
    ) {
    }

    public function direccionRepo(string $obj_dir): DireccionCentroRepositoryInterface|DireccionCentroDlRepositoryInterface|DireccionCentroExRepositoryInterface|DireccionCasaRepositoryInterface|DireccionCasaDlRepositoryInterface|DireccionCasaExRepositoryInterface
    {
        return match ($obj_dir) {
            'DireccionCentro' => $this->direccionCentroRepository,
            'DireccionCentroDl' => $this->direccionCentroDlRepository,
            'DireccionCentroEx' => $this->direccionCentroExRepository,
            'DireccionCdc' => $this->direccionCasaRepository,
            'DireccionCdcDl' => $this->direccionCasaDlRepository,
            'DireccionCdcEx' => $this->direccionCasaExRepository,
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }

    public function ubiRepo(string $obj_dir): CentroRepositoryInterface|CentroDlRepositoryInterface|CentroExRepositoryInterface|CasaRepositoryInterface|CasaDlRepositoryInterface|CasaExRepositoryInterface
    {
        return match ($obj_dir) {
            'DireccionCentro' => $this->centroRepository,
            'DireccionCentroDl' => $this->centroDlRepository,
            'DireccionCentroEx' => $this->centroExRepository,
            'DireccionCdc' => $this->casaRepository,
            'DireccionCdcDl' => $this->casaDlRepository,
            'DireccionCdcEx' => $this->casaExRepository,
            default => throw new \InvalidArgumentException("obj_dir desconocido: $obj_dir"),
        };
    }
}
