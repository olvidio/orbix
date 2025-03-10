<?php

namespace usuarios\domain;

use core\ConfigGlobal;
use usuarios\model\entity\GestorGrupo;
use usuarios\model\entity\Usuario;
use web\ContestarJson;
use web\Hash;

class gruposLista
{

    /**
     * @param false|array|null $oGrupoColeccion
     * @param mixed $Qid_sel
     * @param mixed $Qscroll_id
     * @return array[]
     */
    public static function gruposLista($Qusername): array
    {
        $error_txt = '';
        $jsondata = [];

        $oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
        $miRole = $oMiUsuario->getId_role();

        if ($miRole > 3) {
            // no es administrador
            $error_txt = _("no tiene permisos para ver esto");
        }

        $aWhere = array();
        $aOperador = array();
        if (!empty($Qusername)) {
            $aWhere['usuario'] = $Qusername;
            $aOperador['usuario'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'usuario';

        $oGesGrupos = new GestorGrupo();
        $oGrupoColeccion = $oGesGrupos->getGrupos($aWhere, $aOperador);

        $a_cabeceras = [_("grupo"),
            ['name' => 'accion', 'formatter' => 'clickFormatter']
        ];

        $a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_eliminar(this.form)"];

        $a_valores = [];
        $i = 0;
        foreach ($oGrupoColeccion as $oGrupo) {
            $i++;
            $id_usuario = $oGrupo->getId_usuario();
            $usuario = $oGrupo->getUsuario();

            $pagina = Hash::link(ConfigGlobal::getWeb() . '/frontend/usuarios/controller/grupo_form.php?'
                . http_build_query(array('quien' => 'grupo', 'id_usuario' => $id_usuario))
            );

            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = array('ira' => $pagina, 'valor' => 'editar');
        }

        $data = ['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}