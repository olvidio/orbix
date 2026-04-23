<?php

namespace src\zonassacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ZonaCtrLista
{
    public static function execute(string $id_zona): array
    {
        $aWhere = [];
        $aOperador = [];
        $cCentros = [];
        switch ($id_zona) {
            case 'no':
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = '';
                $aOperador['id_zona'] = 'IS NULL';
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->getCentros($aWhere, $aOperador);
                break;
            case 'no_sf':
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = '';
                $aOperador['id_zona'] = 'IS NULL';
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class)->getCentros($aWhere, $aOperador);
                break;
            default:
                $aWhere['active'] = 't';
                $aWhere['id_zona'] = $id_zona;
                $aWhere['_ordre'] = 'nombre_ubi';
                $cCentrosDl = $GLOBALS['container']->get(CentroDlRepositoryInterface::class)->getCentros($aWhere);
                $cCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class)->getCentros($aWhere);
                $cCentros = array_merge($cCentrosDl, $cCentrosSf);
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $a_valores = [];
        $i = 0;
        foreach ($cCentros as $oCentro) {
            $i++;
            $id_ubi = (string)$oCentro->getId_ubi();
            if ($id_ubi[0] === '2' && !(($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd')))) {
                continue;
            }
            if ($id_ubi[0] === '2') {
                $a_valores[$i]['clase'] = 'tono2';
            }
            // En las ramas `no` / `no_sf` los centros no tienen zona
            // asignada (`id_zona IS NULL`), por lo que `findById(null)`
            // rompe la firma `int`. Saltamos la busqueda y dejamos el
            // nombre vacio cuando no hay zona.
            $idZonaCentro = $oCentro->getId_zona();
            $oZona = $idZonaCentro !== null ? $ZonaRepository->findById($idZonaCentro) : null;
            $a_valores[$i]['sel'] = $id_ubi;
            $a_valores[$i][1] = $oCentro->getNombre_ubi();
            $a_valores[$i][2] = $oZona?->getNombre_zona() ?? '';
        }

        return [
            'tipo' => 'tabla',
            'id_tabla' => 'zona_ctr_ajax',
            'a_cabeceras' => [_("centro"), _("zona")],
            'a_botones' => 'ninguno',
            'a_valores' => $a_valores,
            'error' => '',
        ];
    }
}
