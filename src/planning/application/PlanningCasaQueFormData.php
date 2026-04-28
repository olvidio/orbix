<?php

declare(strict_types=1);

namespace src\planning\application;

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
     * @return array{filtro: array<string, mixed>, modo_casas: ?string}
     */
    public static function execute(): array
    {
        $oMiUsuario = $_SESSION['session_auth']['MiUsuario'];
        $oRole = new Role();
        $oRole->setId_role($oMiUsuario->getId_role());
        $miSfsv = ConfigGlobal::mi_sfsv();

        $filtro = ['active' => true];

        if ($oRole->isRolePau(PauType::PAU_CDC)) {
            $id_pau = $oMiUsuario->getCsv_id_pau();
            $filtro['id_ubi_in'] = array_values(array_filter(
                array_map('intval', explode(',', (string)$id_pau)),
                static fn ($v) => $v > 0
            ));

            return ['filtro' => $filtro, 'modo_casas' => 'casa'];
        }
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
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

        return ['filtro' => $filtro, 'modo_casas' => null];
    }
}
