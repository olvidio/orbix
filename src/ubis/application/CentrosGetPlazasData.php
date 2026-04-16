<?php

namespace src\ubis\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class CentrosGetPlazasData
{
    public static function execute(): array
    {
        $permiso = 'modificar';
        $oGesCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);

        $c = 0;
        $a_valores = [];
        foreach ($cCentrosDl as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $num_habit_indiv = $oCentro->getNum_habit_indiv();
            $plazas = $oCentro->getPlazas();
            $sede = ($oCentro->isSede()) ? _("si") : _("no");

            if ($permiso === 'modificar') {
                $script = "fnjs_modificar($id_ubi,\"plazas\")";
                $a_valores[$c][1] = ['script' => $script, 'valor' => $nombre_ubi];
            } else {
                $a_valores[$c][1] = $nombre_ubi;
            }
            $a_valores[$c][2] = $num_habit_indiv;
            $a_valores[$c][3] = $plazas;
            $a_valores[$c][4] = $sede;
        }

        $a_cabeceras = [];
        $a_cabeceras[] = ['name' => ucfirst(_("centro")), 'width' => 100, 'formatter' => 'clickFormatter'];
        $a_cabeceras[] = ucfirst(_("número de habitaciones individuales"));
        $a_cabeceras[] = ucfirst(_("plazas"));
        $a_cabeceras[] = ucfirst(_("sede"));

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}

