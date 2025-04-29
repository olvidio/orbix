<?php

namespace src\usuarios\application;

use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

class usuarioEliminar
{
    static public function eliminarFromAray(array $a_sel)
    {
        $error_txt = '';

        if (!empty($a_sel)) { //vengo de un checkbox
            $id_usuario = (integer)strtok($a_sel[0], "#");
        }
        $UsuarioRepository = new UsuarioRepository();
        $oUsuario = $UsuarioRepository->findById($id_usuario);
        if ($UsuarioRepository->Eliminar($oUsuario) === false) {
            $error_txt .= _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $UsuarioRepository->getErrorTxt();
        }

        ContestarJson::enviar($error_txt, 'ok');
    }
}