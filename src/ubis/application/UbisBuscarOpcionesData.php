<?php

namespace src\ubis\application;

use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

/**
 * Opciones de formulario para frontend/ubis/controller/ubis_buscar.php
 *
 * @return array{
 *     opciones_region: array<string, string>,
 *     opciones_tipo_ctr: array<string, string>,
 *     opciones_tipo_casa: array<string, string>,
 *     opciones_pais: array<string, string>
 * }
 */
class UbisBuscarOpcionesData
{
    public static function execute(): array
    {
        $repo = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);

        return [
            'opciones_region' => RegionDropdown::activasOrdenNombre(),
            'opciones_tipo_ctr' => TipoCentroDropdown::listaTiposCentro(),
            'opciones_tipo_casa' => TipoCasaDropdown::listaTiposCasa(),
            'opciones_pais' => $repo->getArrayPaises(),
        ];
    }
}
