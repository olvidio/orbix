<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;

/**
 * Mutacion: elimina una `RelacionTarifaTipoActividad`.
 *
 * Sucesor de la rama `eliminar` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_ajax.php`.
 */
final class RelacionTarifaEliminar
{
    public static function execute(array $input): string
    {
        $id_item = (int)($input['id_item'] ?? 0);
        if ($id_item === 0) {
            return (string)_("no sé cuál he de borrar");
        }

        $repo = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $oRelacion = $repo->findById($id_item);
        if ($oRelacion === null) {
            return (string)_("no se encuentra la relación");
        }

        if ($repo->Eliminar($oRelacion) === false) {
            return (string)_("hay un error, no se ha borrado")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
