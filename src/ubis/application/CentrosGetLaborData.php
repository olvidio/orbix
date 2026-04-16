<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\CuadrosLabor;

class CentrosGetLaborData
{
    public static function execute(): array
    {
        $oPermActiv = new CuadrosLabor();
        $oGesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);

        $a_valores = [];
        foreach ($cCentrosDl as $oCentro) {
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $tipo_ctr = $oCentro->getTipo_ctr();
            $tipo_labor = $oCentro->getTipo_labor();

            $a_valores[] = [
                'id_ubi' => $id_ubi,
                'nombre_ubi' => $nombre_ubi,
                'tipo_ctr' => $tipo_ctr,
                'tipo_labor_txt' => $oPermActiv->cuadros_check_read($tipo_labor),
            ];
        }

        $a_cabeceras = [
            ['name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter'],
            ucfirst(_("tipo de centro")),
            ['name' => ucfirst(_("tipo de labor")), 'width' => 200, 'formatter' => 'clickFormatter2'],
        ];

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}

