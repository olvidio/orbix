<?php

namespace src\cartaspresentacion\application;

use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

/**
 * Data builder: opciones de formulario para la pantalla de busqueda de
 * cartas de presentacion (`frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`).
 *
 * URL del listado y `hash_lista_html`: {@see \frontend\cartaspresentacion\helpers\CartasPresentacionBuscarOpcionesRender}.
 *
 * Sucesor de la parte de inicializacion de desplegables de
 * `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
 */
final class CartasPresentacionBuscarOpcionesData
{
    public function __construct(
        private DireccionCentroRepositoryInterface $direccionCentroRepository,
    ) {
    }

    /**
     * @return array{
     *   opciones_region: array<string, string>,
     *   opciones_pais: array<string, string>,
     *   opciones_delegacion: array<string, string>,
     *   paths: array{lista: string},
     *   hash_lista: array{campos_hidden: array<string, string>, campos_form: string, campos_no: string}
     * }
     */
    public function execute(): array
    {
        return [
            'opciones_region' => RegionDropdown::activasOrdenNombre(),
            'opciones_pais' => (array)$this->direccionCentroRepository->getArrayPaises(),
            'opciones_delegacion' => DelegacionDropdown::byRegiones(['H']),
            'paths' => [
                'lista' => 'frontend/cartaspresentacion/controller/cartas_presentacion_lista.php',
            ],
            'hash_lista' => [
                'campos_hidden' => ['que' => 'get'],
                'campos_form' => 'que!poblacion!region!pais!dl',
                'campos_no' => 'scroll_id!sel',
            ],
        ];
    }
}
