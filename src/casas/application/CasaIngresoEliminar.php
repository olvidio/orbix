<?php

namespace src\casas\application;

use src\casas\domain\contracts\IngresoRepositoryInterface;

/**
 * Use case: eliminar el Ingreso asociado a una actividad.
 *
 * Sucesor de la rama `que=eliminar` de
 * `apps/casas/controller/casa_ajax.php`.
 */
final class CasaIngresoEliminar
{
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return ['ok' => false, 'mensaje' => (string)_("no sé cuál he de borar"), 'data' => ''];
        }
        $Ingreso = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $oIngreso = $Ingreso->findById($id_activ);
        if ($oIngreso === null) {
            return ['ok' => false, 'mensaje' => (string)_("Ingreso no encontrado"), 'data' => ''];
        }
        if ($Ingreso->Eliminar($oIngreso) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha eliminado"), 'data' => ''];
        }
        return ['ok' => true, 'mensaje' => '', 'data' => ''];
    }
}
