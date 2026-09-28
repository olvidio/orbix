<?php

namespace src\usuarios\application;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

class usuarioEliminar
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
    ) {
    }

    /**
     * @param int $id_usuario Identidad ya extraída del `ctx_eliminar` (HashB) por el controlador.
     *
     * @return array{error: string, data: string}
     */
    public function execute(int $id_usuario): array
    {
        $error_txt = '';

        $oUsuario = $this->usuarioRepository->findById($id_usuario);
        if ($oUsuario === null) {
            return ['error' => _('Usuario no encontrado'), 'data' => 'ok'];
        }
        if ($this->usuarioRepository->Eliminar($oUsuario) === false) {
            $error_txt .= _('hay un error, no se ha eliminado');
            $error_txt .= "\n" . $this->usuarioRepository->getErrorTxt();
        }

        return ['error' => $error_txt, 'data' => 'ok'];
    }
}
