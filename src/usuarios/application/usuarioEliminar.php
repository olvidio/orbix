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
     * @param list<string> $a_sel
     *
     * @return array{error: string, data: string}
     */
    public function execute(array $a_sel): array
    {
        $error_txt = '';
        $id_usuario = 0;

        if ($a_sel !== []) {
            $id_usuario = (int)strtok($a_sel[0], '#');
        }

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
