<?php

namespace src\ubis\application\services;

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
use src\ubis\domain\contracts\RelacionCasaDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCasaExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDlDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionUbiDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCdcRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrExRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;

/**
 * Resuelve repositorios de ubi/teleco/dirección por tipo de entidad (obj_pau / obj_dir).
 */
final class UbiRepositoryResolver
{
    public function __construct(
        private CentroRepositoryInterface $centroRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroExRepositoryInterface $centroExRepository,
        private CasaRepositoryInterface $casaRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
        private CasaExRepositoryInterface $casaExRepository,
        private TelecoCtrRepositoryInterface $telecoCtrRepository,
        private TelecoCtrDlRepositoryInterface $telecoCtrDlRepository,
        private TelecoCtrExRepositoryInterface $telecoCtrExRepository,
        private TelecoCdcRepositoryInterface $telecoCdcRepository,
        private TelecoCdcDlRepositoryInterface $telecoCdcDlRepository,
        private TelecoCdcExRepositoryInterface $telecoCdcExRepository,
        private DireccionCentroRepositoryInterface $direccionCentroRepository,
        private DireccionCentroDlRepositoryInterface $direccionCentroDlRepository,
        private DireccionCentroExRepositoryInterface $direccionCentroExRepository,
        private DireccionCasaRepositoryInterface $direccionCasaRepository,
        private DireccionCasaDlRepositoryInterface $direccionCasaDlRepository,
        private DireccionCasaExRepositoryInterface $direccionCasaExRepository,
        private RelacionCentroDireccionRepositoryInterface $relacionCentroDireccionRepository,
        private RelacionCentroDlDireccionRepositoryInterface $relacionCentroDlDireccionRepository,
        private RelacionCentroExDireccionRepositoryInterface $relacionCentroExDireccionRepository,
        private RelacionCasaDireccionRepositoryInterface $relacionCasaDireccionRepository,
        private RelacionCasaDlDireccionRepositoryInterface $relacionCasaDlDireccionRepository,
        private RelacionCasaExDireccionRepositoryInterface $relacionCasaExDireccionRepository,
    ) {
    }

    public function getRepository(string $entityType): CasaRepositoryInterface|CentroRepositoryInterface|CentroDlRepositoryInterface|CentroExRepositoryInterface|CasaDlRepositoryInterface|CasaExRepositoryInterface
    {
        return match ($entityType) {
            'Centro' => $this->centroRepository,
            'CentroDl' => $this->centroDlRepository,
            'CentroEx' => $this->centroExRepository,
            'Casa' => $this->casaRepository,
            'CasaDl' => $this->casaDlRepository,
            'CasaEx' => $this->casaExRepository,
            default => throw new \InvalidArgumentException("Repository for entity type '$entityType' not found"),
        };
    }

    /**
     * Carga el ubi para comprobar permisos de edición (incluye fallback ctrsf/cdcsf en tabla general).
     */
    public function findUbiForPermisos(string $obj, int $idUbi): ?object
    {
        $objPau = UbiPermisos::normalizeObjPau($obj);
        if (!in_array($objPau, ['CentroDl', 'CasaDl', 'CentroEx', 'CasaEx'], true)) {
            return null;
        }

        $oUbi = $this->getRepository($objPau)->findById($idUbi);
        if ($oUbi !== null) {
            return $oUbi;
        }

        return match ($objPau) {
            'CentroDl' => $this->centroRepository->findById($idUbi),
            'CasaDl' => $this->casaRepository->findById($idUbi),
            default => null,
        };
    }

    public function getMetodo(string $entityType): string
    {
        return match ($entityType) {
            'Centro', 'CentroDl', 'CentroEx' => 'getCentros',
            'Casa', 'CasaDl', 'CasaEx' => 'getCasas',
            default => throw new \InvalidArgumentException("Method for entity type '$entityType' not found"),
        };
    }

    /**
     * @return class-string
     */
    public function getDireccionRepositoryClass(string $entityType): string
    {
        return match ($entityType) {
            'Centro' => DireccionCentroRepositoryInterface::class,
            'CentroDl' => DireccionCentroDlRepositoryInterface::class,
            'CentroEx' => DireccionCentroExRepositoryInterface::class,
            'Casa' => DireccionCasaRepositoryInterface::class,
            'CasaDl' => DireccionCasaDlRepositoryInterface::class,
            'CasaEx' => DireccionCasaExRepositoryInterface::class,
            default => throw new \InvalidArgumentException("Address repository for entity type '$entityType' not found"),
        };
    }

    public function getDireccionRepository(string $entityType): DireccionCentroRepositoryInterface|DireccionCentroDlRepositoryInterface|DireccionCentroExRepositoryInterface|DireccionCasaRepositoryInterface|DireccionCasaDlRepositoryInterface|DireccionCasaExRepositoryInterface
    {
        return match ($entityType) {
            'Centro' => $this->direccionCentroRepository,
            'CentroDl' => $this->direccionCentroDlRepository,
            'CentroEx' => $this->direccionCentroExRepository,
            'Casa' => $this->direccionCasaRepository,
            'CasaDl' => $this->direccionCasaDlRepository,
            'CasaEx' => $this->direccionCasaExRepository,
            default => throw new \InvalidArgumentException("Address repository for entity type '$entityType' not found"),
        };
    }

    public function getDireccionRepositoryByInterface(string $interfaceClass): DireccionCentroRepositoryInterface|DireccionCentroDlRepositoryInterface|DireccionCentroExRepositoryInterface|DireccionCasaRepositoryInterface|DireccionCasaDlRepositoryInterface|DireccionCasaExRepositoryInterface
    {
        return match ($interfaceClass) {
            DireccionCentroRepositoryInterface::class => $this->direccionCentroRepository,
            DireccionCentroDlRepositoryInterface::class => $this->direccionCentroDlRepository,
            DireccionCentroExRepositoryInterface::class => $this->direccionCentroExRepository,
            DireccionCasaRepositoryInterface::class => $this->direccionCasaRepository,
            DireccionCasaDlRepositoryInterface::class => $this->direccionCasaDlRepository,
            DireccionCasaExRepositoryInterface::class => $this->direccionCasaExRepository,
            default => throw new \InvalidArgumentException("Address repository for interface '$interfaceClass' not found"),
        };
    }

    /**
     * @param class-string $direccionRepoClass
     */
    public function getRelacionRepositoryForDireccion(string $direccionRepoClass): RelacionCentroDireccionRepositoryInterface|RelacionCentroDlDireccionRepositoryInterface|RelacionCentroExDireccionRepositoryInterface|RelacionCasaDireccionRepositoryInterface|RelacionCasaDlDireccionRepositoryInterface|RelacionCasaExDireccionRepositoryInterface
    {
        return match ($direccionRepoClass) {
            DireccionCentroDlRepositoryInterface::class => $this->relacionCentroDlDireccionRepository,
            DireccionCentroExRepositoryInterface::class => $this->relacionCentroExDireccionRepository,
            DireccionCentroRepositoryInterface::class => $this->relacionCentroDireccionRepository,
            DireccionCasaDlRepositoryInterface::class => $this->relacionCasaDlDireccionRepository,
            DireccionCasaExRepositoryInterface::class => $this->relacionCasaExDireccionRepository,
            DireccionCasaRepositoryInterface::class => $this->relacionCasaDireccionRepository,
            default => throw new \InvalidArgumentException("Relation repository for '$direccionRepoClass' not found"),
        };
    }

    /**
     * @return class-string
     */
    public function getTelecoRepositoryClass(string $entityType): string
    {
        return match ($entityType) {
            'Centro' => TelecoCtrRepositoryInterface::class,
            'CentroDl' => TelecoCtrDlRepositoryInterface::class,
            'CentroEx' => TelecoCtrExRepositoryInterface::class,
            'Casa' => TelecoCdcRepositoryInterface::class,
            'CasaDl' => TelecoCdcDlRepositoryInterface::class,
            'CasaEx' => TelecoCdcExRepositoryInterface::class,
            default => throw new \InvalidArgumentException("Teleco repository for entity type '$entityType' not found"),
        };
    }

    public function getTelecoRepository(string $entityType): TelecoCtrRepositoryInterface|TelecoCtrDlRepositoryInterface|TelecoCtrExRepositoryInterface|TelecoCdcRepositoryInterface|TelecoCdcDlRepositoryInterface|TelecoCdcExRepositoryInterface
    {
        return match ($entityType) {
            'Centro' => $this->telecoCtrRepository,
            'CentroDl' => $this->telecoCtrDlRepository,
            'CentroEx' => $this->telecoCtrExRepository,
            'Casa' => $this->telecoCdcRepository,
            'CasaDl' => $this->telecoCdcDlRepository,
            'CasaEx' => $this->telecoCdcExRepository,
            default => throw new \InvalidArgumentException("Teleco repository for entity type '$entityType' not found"),
        };
    }
}
