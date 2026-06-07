<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

/**
 * Caso de uso: datos para la pantalla `procesos_select`.
 */
class ProcesosSelectData
{
    public function __construct(
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
    }

    /**
     * @return array{a_tipos_proceso: array<int|string, string>}
     */
    public function execute(): array
    {
        return [
            'a_tipos_proceso' => $this->procesoTipoRepository->getArrayProcesoTipos(),
        ];
    }
}
