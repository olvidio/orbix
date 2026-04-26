<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiPermisos;
use function src\shared\domain\helpers\is_true;

final class DireccionesEditarData
{
    public static function execute(int $id_ubi, string $mod, string $obj_dir, string $id_direccion_csv, int $idx, string $inc): array
    {
        $repoUbi = DireccionesResolver::ubiRepo($obj_dir);
        $oUbi = $repoUbi->findById($id_ubi);

        $data = [
            'sin_direccion' => false,
            'msg_sin_direccion' => _("este ubi no dispone de una dirección. Compruebe primero si existe, en este caso, asígnesela. En caso contrario cree una nueva."),
            'idx' => $idx,
            'id_direccion' => $id_direccion_csv,
            'nom_sede' => '',
            'direccion' => '',
            'a_p' => '',
            'c_p' => '',
            'poblacion' => '',
            'provincia' => '',
            'pais' => '',
            'observ' => '',
            'f_direccion' => '',
            'latitud' => '',
            'longitud' => '',
            'cp_dcha' => false,
            'propietario' => false,
            'principal' => false,
            'id_direccion_actual' => '',
            'mas' => 0,
            'menos' => 0,
            'botones' => '0',
            'obj' => 'ubis\\model\\entity\\' . $obj_dir,
        ];

        if ($mod === 'nuevo') {
            $data['idx'] = -1;
        } else {
            $a_id_direccion = ($id_direccion_csv !== '') ? explode(',', $id_direccion_csv) : [];
            $num_dir = count($a_id_direccion);
            if ($num_dir === 0) {
                $data['sin_direccion'] = true;
                return $data;
            }
            if ($inc === 'mas' && $idx < $num_dir - 1) {
                $idx++;
            }
            if ($inc === 'menos' && $idx > 0) {
                $idx--;
            }
            $id_direccion_actual = $a_id_direccion[$idx] ?? '';
            if ($id_direccion_actual === '') {
                $data['sin_direccion'] = true;
                return $data;
            }
            $oDireccionDetallada = $oUbi->getUnaDireccionDetallada((int)$id_direccion_actual);
            if ($oDireccionDetallada === null) {
                $data['sin_direccion'] = true;
                return $data;
            }
            $oDireccion = $oDireccionDetallada->getDireccionVo();
            $data['idx'] = $idx;
            $data['id_direccion_actual'] = $id_direccion_actual;
            $data['nom_sede'] = $oDireccion->getNom_sede();
            $data['direccion'] = $oDireccion->getDireccionVo()?->value() ?? '';
            $data['a_p'] = $oDireccion->getA_p();
            $data['c_p'] = $oDireccion->getC_p();
            $data['cp_dcha'] = is_true($oDireccion->isCp_dcha());
            $data['poblacion'] = $oDireccion->getPoblacion();
            $data['provincia'] = $oDireccion->getProvincia();
            $data['pais'] = $oDireccion->getPais();
            $data['observ'] = $oDireccion->getObserv();
            $data['f_direccion'] = $oDireccion->getF_direccion()?->getFromLocal();
            $data['latitud'] = $oDireccion->getLatitud();
            $data['longitud'] = $oDireccion->getLongitud();
            $data['propietario'] = is_true($oDireccionDetallada->isPropietario());
            $data['principal'] = is_true($oDireccionDetallada->isPrincipal());
            $data['mas'] = ($idx < $num_dir - 1) ? 1 : 0;
            $data['menos'] = ($idx < 1) ? 0 : 1;
        }

        if (UbiPermisos::puedeModificar($obj_dir, $oUbi)) {
            $data['botones'] = '1,4,5';
        }

        return $data;
    }
}
