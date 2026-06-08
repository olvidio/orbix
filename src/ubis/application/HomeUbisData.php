<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiPermisos;
use src\ubis\application\services\UbiTelecoService;

final class HomeUbisData
{
    public function __construct(
        private UbiFactory $ubiFactory,
        private UbiTelecoService $ubiTelecoService,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_ubi): array
    {
        $oUbi = $this->ubiFactory->newUbi($id_ubi);
        if ($oUbi === null) {
            return [];
        }

        $nombre_ubi = $oUbi->getNombre_ubi();
        $dl = $oUbi->getDl();
        $region = $oUbi->getRegion();
        $tipo_ubi = $oUbi->getTipo_ubi();

        $cDirecciones = $oUbi->getDirecciones();
        $d = 0;
        $direccion = '';
        $poblacion = '';
        $c_p = '';
        $id_direccion = '';
        foreach ($cDirecciones as $oDireccion) {
            $d++;
            if ($d > 1) {
                $direccion .= '<br>';
                $poblacion .= '<br>';
                $c_p .= '<br>';
                $id_direccion .= ',';
            }
            $direccion .= $oDireccion->getDireccionVo()?->value() ?? '';
            $poblacion .= $oDireccion->getPoblacion();
            $c_p .= $oDireccion->getC_p();
            $id_direccion .= $oDireccion->getId_direccion();
        }
        $id_pau = $id_ubi;
        $pau = "u";

        $es_mi_delegacion = UbiPermisos::dlPerteneceAMiDelegacion($dl);
        switch ($tipo_ubi) {
            case "ctrsf":
            case "ctrdl":
                if (!$es_mi_delegacion) {
                    $obj_pau = "Centro";
                    $obj_dir = "DireccionCentro";
                } else {
                    $obj_pau = "CentroDl";
                    $obj_dir = "DireccionCentroDl";
                }
                $ubi = _("centro");
                break;
            case "ctrex":
                $obj_pau = "CentroEx";
                $obj_dir = "DireccionCentroEx";
                $ubi = _("centro");
                break;
            case "cdcdl":
                if (!$es_mi_delegacion) {
                    $obj_pau = "Casa";
                    $obj_dir = "DireccionCdc";
                } else {
                    $obj_pau = "CasaDl";
                    $obj_dir = "DireccionCdcDl";
                }
                $ubi = _("casa");
                break;
            case "cdcex":
                $obj_pau = "CasaEx";
                $obj_dir = "DireccionCdcEx";
                $ubi = _("casa");
                break;
            default:
                $obj_pau = '';
                $obj_dir = '';
                $ubi = '';
                break;
        }

        $telfs = $this->ubiTelecoService->texto($obj_pau, $id_ubi, 'telf', '*', ' / ');
        $fax = $this->ubiTelecoService->texto($obj_pau, $id_ubi, 'fax', '*', ' / ');
        $mails = $this->ubiTelecoService->texto($obj_pau, $id_ubi, 'e-mail', '*', ' / ');

        return [
            'id_ubi' => $id_ubi,
            'id_pau' => $id_pau,
            'pau' => $pau,
            'nombre_ubi' => $nombre_ubi,
            'dl' => $dl,
            'region' => $region,
            'direccion' => $direccion,
            'poblacion' => $poblacion,
            'c_p' => $c_p,
            'id_direccion' => $id_direccion,
            'obj_pau' => $obj_pau,
            'obj_dir' => $obj_dir,
            'ubi' => $ubi,
            'telfs' => $telfs,
            'fax' => $fax,
            'mails' => $mails,
        ];
    }
}
