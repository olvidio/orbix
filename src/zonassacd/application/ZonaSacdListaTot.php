<?php

namespace src\zonassacd\application;

use src\shared\config\ConfigGlobal;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

class ZonaSacdListaTot
{
    public static function execute(): array
    {
        $mi_dl = ConfigGlobal::mi_delef();
        $aWhere = ['sacd' => 't', 'dl' => $mi_dl, '_ordre' => 'apellido1,apellido2,nom'];
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $cSacds = $PersonaSacdRepository->getPersonas($aWhere);
        $a_valores = [];
        $i = 0;
        foreach ($cSacds as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom]);
            $a_zonas = [];
            foreach ($cZonaSacd as $oZonaSacd) {
                $id_zona = $oZonaSacd->getId_zona();
                $propia = $oZonaSacd->isPropia();
                $oZona = $ZonaRepository->findById($id_zona);
                $orden = $propia === true ? 0 : $oZona?->getOrden() ?? 0;
                $a_zonas[$orden] = [$oZona?->getNombre_zona() ?? '', $propia];
            }
            if (count($a_zonas) >= 1) {
                ksort($a_zonas);
                foreach ($a_zonas as $a_zona) {
                    $a_valores[$i][1] = $ap_nom;
                    $a_valores[$i][2] = $a_zona[0];
                    $a_valores[$i][3] = empty($a_zona[1]) ? _("no") : _("si");
                    $i++;
                }
            } else {
                $a_valores[$i][1] = $ap_nom;
                $a_valores[$i][2] = '';
                $a_valores[$i][3] = '';
            }
            $i++;
        }
        return [
            'tipo' => 'lista',
            'a_cabeceras' => [_("sacd"), _("zona"), _("propia")],
            'a_valores' => $a_valores,
            'error' => '',
        ];
    }
}
