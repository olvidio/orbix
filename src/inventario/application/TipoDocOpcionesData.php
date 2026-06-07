<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;

/**
 * Opciones del desplegable de tipos de documento (`lista_tipo_doc.php`).
 */
final class TipoDocOpcionesData
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, mixed>}
     */
    public function execute(): array
    {
        return [
            'a_opciones' => $this->tipoDocRepository->getArrayTipoDoc(),
        ];
    }
}
