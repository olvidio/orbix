<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

/**
 * Mutacion: elimina un `TipoTarifa`.
 *
 * Sucesor de la rama `tar_eliminar` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TipoTarifaEliminar
{
    public static function execute(array $input): string
    {
        $id_tarifa = (int)($input['id_tarifa'] ?? 0);
        if ($id_tarifa === 0) {
            return (string)_("no sé cuál he de borrar");
        }

        $repo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $oTipoTarifa = $repo->findById($id_tarifa);
        if ($oTipoTarifa === null) {
            return (string)_("no se encuentra la tarifa");
        }

        if ($repo->Eliminar($oTipoTarifa) === false) {
            return (string)_("hay un error, no se ha borrado");
        }

        return '';
    }
}
