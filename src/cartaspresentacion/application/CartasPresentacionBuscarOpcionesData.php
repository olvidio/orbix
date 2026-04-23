<?php

namespace src\cartaspresentacion\application;

use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

/**
 * Data builder: opciones de formulario para la pantalla de busqueda de
 * cartas de presentacion (`frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`).
 *
 * Sucesor de la parte de inicializacion de desplegables de
 * `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
 */
final class CartasPresentacionBuscarOpcionesData
{
    /**
     * @return array{
     *   opciones_region: array<string,string>,
     *   opciones_pais: array<string,string>,
     *   opciones_delegacion: array<string,string>
     * }
     */
    public static function execute(): array
    {
        $repoDireccion = $GLOBALS['container']->get(DireccionCentroRepositoryInterface::class);

        return [
            'opciones_region' => RegionDropdown::activasOrdenNombre(),
            'opciones_pais' => (array)$repoDireccion->getArrayPaises(),
            'opciones_delegacion' => DelegacionDropdown::byRegiones(['H']),
        ];
    }
}
