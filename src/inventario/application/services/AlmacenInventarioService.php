<?php

declare(strict_types=1);

namespace src\inventario\application\services;

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;

/**
 * Centro de inventario que hace de almacén de la dl: de ahí salen los documentos y las maletas que se
 * pueden añadir a un equipaje.
 *
 * Es el parámetro de configuración del esquema {@see self::PARAMETRO} (pantalla de parámetros); antes
 * era un id fijo en el código que solo valía para una dl.
 */
final class AlmacenInventarioService
{
    public const PARAMETRO = 'inventario_ubi_almacen';

    public function __construct(
        private ConfigSchemaRepositoryInterface $configSchemaRepository,
    ) {
    }

    /** Id del centro de inventario que hace de almacén, o null si no se ha definido. */
    public function idUbi(): ?int
    {
        $valor = trim((string) $this->configSchemaRepository->findById(self::PARAMETRO)?->getValorVo()?->value());
        if ($valor === '' || !ctype_digit($valor) || (int) $valor === 0) {
            return null;
        }

        return (int) $valor;
    }

    /** Aviso para la pantalla cuando falta el parámetro. */
    public function avisoSinDefinir(): string
    {
        return _("Falta definir el almacén de inventario en los parámetros de configuración.");
    }
}
