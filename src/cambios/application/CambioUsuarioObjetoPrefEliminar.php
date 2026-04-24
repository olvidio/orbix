<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;

/**
 * Mutacion: elimina un `CambioUsuarioObjetoPref` por id. Sucesor de la rama
 * `eliminar` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioObjetoPrefEliminar
{
    /**
     * @param array $input
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto'] ?? 0);
        if ($id_item_usuario_objeto <= 0) {
            return ['error' => (string)_("falta id_item_usuario_objeto")];
        }

        $Repo = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $oPref = $Repo->findById($id_item_usuario_objeto);
        if ($oPref === null) {
            return ['error' => (string)_("preferencia no encontrada")];
        }
        if ($Repo->Eliminar($oPref) === false) {
            return ['error' => (string)_("Hay un error, no se ha eliminado")];
        }
        return ['error' => ''];
    }
}
