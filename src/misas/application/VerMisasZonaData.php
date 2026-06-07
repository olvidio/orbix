<?php

declare(strict_types=1);

namespace src\misas\application;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * @see misas_ver_misas_zona_build()
 */
class VerMisasZonaData
{
    public function __construct(
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly EncargoDiaRepositoryInterface $encargoDiaRepository,
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }

    public function getPersonaSacdRepository(): PersonaSacdRepositoryInterface
    {
        return $this->personaSacdRepository;
    }

    public function getZonaSacdRepository(): ZonaSacdRepositoryInterface
    {
        return $this->zonaSacdRepository;
    }

    public function getEncargoDiaRepository(): EncargoDiaRepositoryInterface
    {
        return $this->encargoDiaRepository;
    }

    public function getEncargoHorarioRepository(): EncargoHorarioRepositoryInterface
    {
        return $this->encargoHorarioRepository;
    }

    public function getEncargoRepository(): EncargoRepositoryInterface
    {
        return $this->encargoRepository;
    }

    /**
     * @param array<string, mixed> $in
     * @return array<string, mixed>
     */
    public function build(array $in): array
    {
        require_once __DIR__ . '/ver_misas_zona_data_build.php';

        return \misas_ver_misas_zona_build($in, $this);
    }
}
