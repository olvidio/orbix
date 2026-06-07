<?php

declare(strict_types=1);

namespace src\misas\application;

use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;

/**
 * @see misas_crear_nuevo_periodo_build()
 */
class CrearNuevoPeriodoData
{
    public function __construct(
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly ActividadRepositoryInterface $actividadRepository,
        private readonly InicialesSacdService $inicialesSacdService,
        private readonly ActividadCargoRepositoryInterface $actividadCargoRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
        private readonly EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private readonly EncargoTipoRepositoryInterface $encargoTipoRepository,
        private readonly EncargoDiaRepositoryInterface $encargoDiaRepository,
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
    ) {
    }

    public function getZonaSacdRepository(): ZonaSacdRepositoryInterface
    {
        return $this->zonaSacdRepository;
    }

    public function getActividadRepository(): ActividadRepositoryInterface
    {
        return $this->actividadRepository;
    }

    public function getInicialesSacdService(): InicialesSacdService
    {
        return $this->inicialesSacdService;
    }

    public function getActividadCargoRepository(): ActividadCargoRepositoryInterface
    {
        return $this->actividadCargoRepository;
    }

    public function getEncargoRepository(): EncargoRepositoryInterface
    {
        return $this->encargoRepository;
    }

    public function getEncargoSacdHorarioRepository(): EncargoSacdHorarioRepositoryInterface
    {
        return $this->encargoSacdHorarioRepository;
    }

    public function getEncargoTipoRepository(): EncargoTipoRepositoryInterface
    {
        return $this->encargoTipoRepository;
    }

    public function getEncargoDiaRepository(): EncargoDiaRepositoryInterface
    {
        return $this->encargoDiaRepository;
    }

    public function getEncargoHorarioRepository(): EncargoHorarioRepositoryInterface
    {
        return $this->encargoHorarioRepository;
    }

    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public function build(array $in): array
    {
        require_once __DIR__ . '/crear_nuevo_periodo_data_build.php';

        return \misas_crear_nuevo_periodo_build($in, $this);
    }
}
