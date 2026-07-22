<?php

declare(strict_types=1);

namespace src\encargossacd\application;

use src\encargossacd\application\services\EncargoAplicacionService;

/**
 * Opciones del desplegable de grupo de ctrs (`getArraySeccion`) para pantallas frontend
 * sin resolver DI en el árbol `frontend/`.
 */
final class OpcionesSeccionData
{
    public function __construct(
        private EncargoAplicacionService $aplicacionService,
    ) {
    }

    /**
     * @return array{opciones: array<string, string>}
     */
    public function execute(): array
    {
        return [
            'opciones' => $this->aplicacionService->getArraySeccion(),
        ];
    }
}
