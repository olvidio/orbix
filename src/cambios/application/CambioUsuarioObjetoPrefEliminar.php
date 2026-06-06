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
    public function __construct(
        private CambioUsuarioObjetoPrefRepositoryInterface $cambioUsuarioObjetoPrefRepository,
    ) {
    }

    /**
     * @param array{id_item_usuario_objeto?: int|string} $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto'] ?? 0);
        if ($id_item_usuario_objeto <= 0) {
            return ['error' => (string)_("falta id_item_usuario_objeto")];
        }

        $oPref = $this->cambioUsuarioObjetoPrefRepository->findById($id_item_usuario_objeto);
        if ($oPref === null) {
            return ['error' => (string)_("preferencia no encontrada")];
        }
        if ($this->cambioUsuarioObjetoPrefRepository->Eliminar($oPref) === false) {
            return ['error' => (string)_("Hay un error, no se ha eliminado")];
        }
        return ['error' => ''];
    }
}
