<?php

namespace src\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\personas\domain\entity\Persona;

/**
 * Devuelve los sacd encargados actuales de una actividad junto con el flag
 * `permite_modificar` (calculado a partir de `PermisosActividades`) para
 * que el frontend decida si pinta cada sacd como link clicable
 * (`fnjs_cambiar_sacd`) o como texto plano.
 *
 * Sucesor de la rama `get` del dispatcher legacy
 * `apps/actividadessacd/controller/activ_sacd_ajax.php`.
 */
final class SacdsEncargadosData
{
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ <= 0) {
            return [
                'id_activ' => 0,
                'permite_ver' => false,
                'permite_modificar' => false,
                'sacds' => [],
            ];
        }
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $dl_org = (string)($input['dl_org'] ?? '');

        $oPermSacd = self::resolverPermisoSacd($id_activ, $id_tipo_activ, $dl_org);
        $permite_ver = $oPermSacd !== null && $oPermSacd->have_perm_activ('ver') === true;
        $permite_modificar = $oPermSacd !== null && $oPermSacd->have_perm_activ('modificar') === true;

        $sacds = [];
        if ($permite_ver) {
            $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
            $aIdCargos_sacd = $CargoRepository->getArrayCargos('sacd');
            $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

            $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
            $cCargos = $ActividadCargoRepository->getActividadCargos(
                ['id_activ' => $id_activ, 'id_cargo' => $txt_where_cargos],
                ['id_cargo' => 'IN']
            );
            if (is_array($cCargos)) {
                foreach ($cCargos as $oCargo) {
                    $id_nom = (int)$oCargo->getId_nom();
                    $oPersona = Persona::findPersonaEnGlobal($id_nom);
                    $ap_nom = is_object($oPersona)
                        ? (string)$oPersona->getPrefApellidosNombre()
                        : (string)$oPersona;
                    $sacds[] = [
                        'id_nom' => $id_nom,
                        'id_cargo' => (int)$oCargo->getId_cargo(),
                        'ap_nom' => $ap_nom,
                    ];
                }
            }
        }

        return [
            'id_activ' => $id_activ,
            'permite_ver' => $permite_ver,
            'permite_modificar' => $permite_modificar,
            'sacds' => $sacds,
        ];
    }

    private static function resolverPermisoSacd(int $id_activ, string $id_tipo_activ, string $dl_org)
    {
        if (ConfigGlobal::is_app_installed('procesos') && isset($_SESSION['oPermActividades'])) {
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            return $_SESSION['oPermActividades']->getPermisoActual('sacd');
        }
        $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        return $oPermActividades->getPermisoActual('sacd');
    }
}
