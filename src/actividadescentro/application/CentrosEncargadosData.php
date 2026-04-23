<?php

namespace src\actividadescentro\application;

use permisos\model\PermisosActividadesTrue;
use core\ConfigGlobal;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;

/**
 * Devuelve el listado de centros encargados actuales de una actividad junto
 * con el flag `permite_modificar` (calculado a partir de
 * `PermisosActividades`) para que el frontend decida si pinta los centros
 * como links (fnjs_cambiar_ctr) o como texto plano.
 *
 * Sucesor de la rama `get` del dispatcher legacy.
 */
final class CentrosEncargadosData
{
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ <= 0) {
            return [
                'id_activ' => 0,
                'permite_ver' => false,
                'permite_modificar' => false,
                'centros' => [],
            ];
        }
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $dl_org = (string)($input['dl_org'] ?? '');

        $oPermCtr = self::resolverPermisoCtr($id_activ, $id_tipo_activ, $dl_org);
        $permite_ver = $oPermCtr !== null && $oPermCtr->have_perm_activ('ver') === true;
        $permite_modificar = $oPermCtr !== null && $oPermCtr->have_perm_activ('modificar') === true;

        $centros = [];
        if ($permite_ver) {
            $repo = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
            $cCentros = $repo->getCentrosEncargadosActividad($id_activ);
            foreach ($cCentros as $oCentro) {
                $centros[] = [
                    'id_ubi' => (int)$oCentro->getId_ubi(),
                    'nombre_ubi' => (string)$oCentro->getNombre_ubi(),
                ];
            }
        }

        return [
            'id_activ' => $id_activ,
            'permite_ver' => $permite_ver,
            'permite_modificar' => $permite_modificar,
            'centros' => $centros,
        ];
    }

    private static function resolverPermisoCtr(int $id_activ, string $id_tipo_activ, string $dl_org)
    {
        if (ConfigGlobal::is_app_installed('procesos') && isset($_SESSION['oPermActividades'])) {
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            return $_SESSION['oPermActividades']->getPermisoActual('ctr');
        }
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        return $oPermActividades->getPermisoActual('ctr');
    }
}
