<?php

namespace usuarios\domain;

use Illuminate\Http\JsonResponse;
use usuarios\model\entity\Usuario;
use web\ContestarJson;

class usuarioEliminar
{
    static public function eliminarFromAray(array $a_sel)
    {
        $error_txt = '';

        if (!empty($a_sel)) { //vengo de un checkbox
            $id_usuario = (integer)strtok($a_sel[0], "#");
        }
        $oUsuario = new Usuario(array('id_usuario' => $id_usuario));
        if ($oUsuario->DBEliminar() === false) {
            $error_txt .= _("hay un error, no se ha eliminado");
            $error_txt .= "\n" . $oUsuario->getErrorTxt();
        }

        ContestarJson::enviar($error_txt, 'ok');
    }
}