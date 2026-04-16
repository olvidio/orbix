<?php

namespace src\ubis\application;

use core\ConfigGlobal;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\entity\Ubi;

class HomeUbisData
{
    public static function execute(int $id_ubi): array
    {
        $oUbi = Ubi::NewUbi($id_ubi);
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

        $mi_dele = ConfigGlobal::mi_delef();
        switch ($tipo_ubi) {
            case "ctrsf":
            case "ctrdl":
                if ($dl != $mi_dele) {
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
                if ($dl != $mi_dele) {
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

        $telfs = UbiTelecoService::texto($obj_pau, $id_ubi, 'telf', '*', ' / ');
        $fax = UbiTelecoService::texto($obj_pau, $id_ubi, 'fax', '*', ' / ');
        $mails = UbiTelecoService::texto($obj_pau, $id_ubi, 'e-mail', '*', ' / ');

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

