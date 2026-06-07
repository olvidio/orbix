<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;
use src\ubis\application\services\TipoCasaDropdown;
use src\ubis\application\services\TipoCentroDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;

/**
 * Opciones de desplegables para frontend/ubis/controller/ubis_editar.php
 */
class UbisEditarOpcionesData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroExRepositoryInterface $centroExRepository,
        private DelegacionDropdown $delegacionDropdown,
        private RegionDropdown $regionDropdown,
        private TipoCentroDropdown $tipoCentroDropdown,
        private TipoCasaDropdown $tipoCasaDropdown,
    ) {
    }

    /**
     * @return array{
     *     opciones_dl: array<string, string>,
     *     opciones_region: array<string, string>,
     *     opciones_tipo_ctr: array<string, string>,
     *     opciones_tipo_casa: array<string, string>,
     *     opciones_id_ctr_padre: array<string|int, string>
     * }
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(string $obj_pau, string $tipo_ubi, string $dl, string $region): array
    {
        $isfsv = ConfigGlobal::mi_sfsv();
        $incluirPropia = true;

        if ($obj_pau === 'CasaDl' || $obj_pau === 'Casa' || $obj_pau === 'CasaEx') {
            $opciones_dl = $this->delegacionDropdown->delegacionesURegiones(1, $incluirPropia);
        } else {
            $opciones_dl = $this->delegacionDropdown->delegacionesURegiones($isfsv, $incluirPropia);
        }

        $opciones_id_ctr_padre = [];
        if ($tipo_ubi === 'ctrdl' || $tipo_ubi === 'ctrsf') {
            $sWhere = $this->whereDlRegion($dl, $region);
            $opciones_id_ctr_padre = $this->centroDlRepository->getArrayCentros($sWhere);
        } elseif ($tipo_ubi === 'ctrex') {
            $sWhere = $this->whereDlRegion($dl, $region);
            $opciones_id_ctr_padre = $this->centroExRepository->getArrayCentros($sWhere);
        }

        return [
            'opciones_dl' => $opciones_dl,
            'opciones_region' => $this->regionDropdown->activasOrdenNombre(),
            'opciones_tipo_ctr' => $this->tipoCentroDropdown->listaTiposCentro(),
            'opciones_tipo_casa' => $this->tipoCasaDropdown->listaTiposCasa(),
            'opciones_id_ctr_padre' => $opciones_id_ctr_padre,
        ];
    }

    private function whereDlRegion(string $dl, string $region): string
    {
        if (!empty($dl) && $this->esCodigoValido($dl)) {
            return "WHERE dl = '$dl'";
        }
        if (!empty($region) && $this->esCodigoValido($region)) {
            return "WHERE region = '$region'";
        }

        return '';
    }

    private function esCodigoValido(string $codigo): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9_-]{1,16}$/', $codigo);
    }
}
