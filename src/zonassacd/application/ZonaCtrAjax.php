<?php

namespace src\zonassacd\application;

use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ZonaCtrAjax
{
    public static function execute(string $que, string $id_zona, string $id_zona_new, array $sel): array
    {
        return match ($que) {
            'get_lista' => self::getLista($id_zona),
            'update' => self::update($id_zona_new, $sel),
            default => ['error' => sprintf(_("opción no definida en switch: %s"), $que)],
        };
    }

    private static function getLista(string $id_zona): array
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
            $idZonaCentro = $oCentro->getId_zona();
            $oZona = $ZonaRepository->findById($idZonaCentro);
            $a_valores[$i]['sel'] = $id_ubi;
            $a_valores[$i][1] = $oCentro->getNombre_ubi();
            $a_valores[$i][2] = $oZona->getNombre_zona();
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

    private static function update(string $id_zona_new, array $sel): array
    {
        $errores = [];
        foreach ($sel as $id_ubi) {
            $id_ubi = (string)$id_ubi;
            if ((int)$id_ubi[0] === 1) {
                $CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            } else {
                $CentroRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            }
            $oCentro = $CentroRepository->findById($id_ubi);
            $oCentro->setId_zona($id_zona_new === 'no' ? '' : $id_zona_new);
            if ($CentroRepository->Guardar($oCentro) === false) {
                $errores[] = _("hay un error, no se ha guardado.");
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
