<?php

declare(strict_types=1);

namespace src\planning\application;

use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\PauType;

/**
 * Filtro de casas y modo del selector CasasQue para `planning_casa_que`
 * según rol / permiso del usuario actual (antes en el frontend con `Role`/`PauType`).
 */
final class PlanningCasaQueFormData
{
    /**
     * @return array{filtro: array<string, mixed>, modo_casas: string}
     */
    public function execute(): array
    {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        $oMiUsuario = is_array($sessionAuth) ? ($sessionAuth['MiUsuario'] ?? null) : null;
        if (!is_object($oMiUsuario) || !method_exists($oMiUsuario, 'getId_role')) {
            throw new \RuntimeException(_('No se encuentra el usuario'));
        }

        $oRole = new Role();
        $oRole->setId_role($oMiUsuario->getId_role());
        $miSfsv = ConfigGlobal::mi_sfsv();

        $filtro = ['active' => true];

        if ($oRole->isRolePau(PauType::PAU_CDC)) {
            $id_pau = method_exists($oMiUsuario, 'getCsv_id_pau') ? $oMiUsuario->getCsv_id_pau() : '';
            $filtro['id_ubi_in'] = array_values(array_filter(
                array_map('intval', explode(',', (string)$id_pau)),
                static fn ($v) => $v > 0
            ));

            return ['filtro' => $filtro, 'modo_casas' => 'casa'];
        }

        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))) {
            return ['filtro' => $filtro, 'modo_casas' => 'all'];
        }
        if ($miSfsv === 1) {
            $filtro['sv'] = true;

            return ['filtro' => $filtro, 'modo_casas' => 'sv'];
        }
        if ($miSfsv === 2) {
            $filtro['sf'] = true;

            return ['filtro' => $filtro, 'modo_casas' => 'sf'];
        }

        return ['filtro' => $filtro, 'modo_casas' => 'all'];
    }
}
