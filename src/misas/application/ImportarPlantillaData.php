<?php

declare(strict_types=1);

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

/**
 * @see misas_importar_plantilla_build()
 */
class ImportarPlantillaData
{
    public function __construct(
        private readonly EncargoTipoRepositoryInterface $encargoTipoRepository,
        private readonly EncargoDiaRepositoryInterface $encargoDiaRepository,
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
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
        require_once __DIR__ . '/importar_plantilla_data_build.php';

        return \misas_importar_plantilla_build($in, $this);
    }
}
