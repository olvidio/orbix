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
    /**
     * @return array{
     *     opciones_dl: array<string, string>,
     *     opciones_region: array<string, string>,
     *     opciones_tipo_ctr: array<string, string>,
     *     opciones_tipo_casa: array<string, string>,
     *     opciones_id_ctr_padre: array<string|int, string>
     * }
     */
    public static function execute(string $obj_pau, string $tipo_ubi, string $dl, string $region): array
    {
        $isfsv = ConfigGlobal::mi_sfsv();
        $incluirPropia = true;

        if ($obj_pau === 'CasaDl' || $obj_pau === 'Casa' || $obj_pau === 'CasaEx') {
            $opciones_dl = DelegacionDropdown::delegacionesURegiones(1, $incluirPropia);
        } else {
            $opciones_dl = DelegacionDropdown::delegacionesURegiones($isfsv, $incluirPropia);
        }

        $opciones_id_ctr_padre = [];
        if ($tipo_ubi === 'ctrdl' || $tipo_ubi === 'ctrsf') {
            $sWhere = self::whereDlRegion($dl, $region);
            $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $opciones_id_ctr_padre = $repo->getArrayCentros($sWhere);
        } elseif ($tipo_ubi === 'ctrex') {
            $sWhere = self::whereDlRegion($dl, $region);
            $repo = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
            $opciones_id_ctr_padre = $repo->getArrayCentros($sWhere);
        }

        return [
            'opciones_dl' => $opciones_dl,
            'opciones_region' => RegionDropdown::activasOrdenNombre(),
            'opciones_tipo_ctr' => TipoCentroDropdown::listaTiposCentro(),
            'opciones_tipo_casa' => TipoCasaDropdown::listaTiposCasa(),
            'opciones_id_ctr_padre' => $opciones_id_ctr_padre,
        ];
    }

    private static function whereDlRegion(string $dl, string $region): string
    {
        // Los códigos dl/region son tokens cortos alfanuméricos; cualquier otra
        // cosa se descarta para evitar SQL injection en getArrayCentros().
        if (!empty($dl) && self::esCodigoValido($dl)) {
            return "WHERE dl = '$dl'";
        }
        if (!empty($region) && self::esCodigoValido($region)) {
            return "WHERE region = '$region'";
        }

        return '';
    }

    private static function esCodigoValido(string $codigo): bool
    {
        return (bool)preg_match('/^[A-Za-z0-9_-]{1,16}$/', $codigo);
    }
}
