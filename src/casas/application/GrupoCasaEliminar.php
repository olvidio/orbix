<?php

namespace src\casas\application;

use src\casas\domain\contracts\GrupoCasaRepositoryInterface;

/**
 * Mutación: elimina un `GrupoCasa` por `id_item`.
 *
 * Sucesor de la rama `eliminar` de `apps/casas/controller/grupo_ajax.php`.
 */
final class GrupoCasaEliminar
{
    public static function execute(array $input): string
    {
        $id_item = (int)($input['id_item'] ?? 0);
        if ($id_item === 0) {
            return (string)_("debe indicar el grupo a eliminar");
        }

        $repo = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        $oGrupo = $repo->findById($id_item);
        if ($oGrupo === null) {
            return (string)_("no se encuentra el grupo");
        }

        if ($repo->Eliminar($oGrupo) === false) {
            return (string)_("Hay un error, no se ha eliminado.")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
