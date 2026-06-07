<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;

/**
 * Opciones del desplegable de colecciones (`lista_colecciones.php`).
 */
final class ColeccionesOpcionesData
{
    public function __construct(
        private ColeccionRepositoryInterface $coleccionRepository,
    ) {
    }

    /**
     * @return array{a_opciones: array<int|string, mixed>}
     */
    public function execute(): array
    {
        return [
            'a_opciones' => $this->coleccionRepository->getArrayColecciones(),
        ];
    }
}
