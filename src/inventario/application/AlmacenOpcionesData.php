<?php

declare(strict_types=1);

namespace src\inventario\application;

use src\inventario\application\services\AlmacenInventarioService;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Opciones para elegir, en los parámetros de configuración, el centro de inventario que hace de almacén
 * ({@see AlmacenInventarioService}): los centros que tienen lugares, por nombre.
 */
final class AlmacenOpcionesData
{
    public function __construct(
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
        private AlmacenInventarioService $almacen,
    ) {
    }

    /** @return array{opciones: list<array{0: string, 1: string}>, selected: string} */
    public function execute(): array
    {
        $mapa = [];
        foreach ($this->ubiInventarioRepository->getUbisInventarioLugar(true) as $oUbi) {
            $mapa[$oUbi->getId_ubi()] = $oUbi->getNom_ubi();
        }

        return [
            'opciones' => OpcionesDesplegable::enOrden($mapa),
            'selected' => (string) ($this->almacen->idUbi() ?? ''),
        ];
    }
}
