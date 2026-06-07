<?php

namespace src\ubis\application;

use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;

/**
 * Opciones de formulario para frontend/ubis/controller/ubis_buscar.php
 */
class UbisBuscarOpcionesData
{
    public function __construct(
        private DireccionCentroRepositoryInterface $direccionCentroRepository,
        private RegionDropdown $regionDropdown,
        private TipoCentroDropdown $tipoCentroDropdown,
        private TipoCasaDropdown $tipoCasaDropdown,
    ) {
    }

    /**
     * @return array{
     *     opciones_region: array<string, string>,
     *     opciones_tipo_ctr: array<string, string>,
     *     opciones_tipo_casa: array<string, string>,
     *     opciones_pais: array<string, string>
     * }
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'opciones_region' => $this->regionDropdown->activasOrdenNombre(),
            'opciones_tipo_ctr' => $this->tipoCentroDropdown->listaTiposCentro(),
            'opciones_tipo_casa' => $this->tipoCasaDropdown->listaTiposCasa(),
            'opciones_pais' => $this->direccionCentroRepository->getArrayPaises(),
        ];
    }
}
