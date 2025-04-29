<?php

namespace src\usuarios\application;

use core\ConfigGlobal;
use src\usuarios\application\repositories\GrupoRepository;
use web\Hash;

class GruposLista
{

    public function __invoke($username): array
    {
        $aWhere = [];
        $aOperador = [];
        if (!empty($username)) {
            $aWhere['usuario'] = $username;
            $aOperador['usuario'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'usuario';

        $GrupoRepository = new GrupoRepository();
        $cGrupos = $GrupoRepository->getGrupos($aWhere, $aOperador);

        $a_cabeceras = [_("grupo"),
            ['name' => 'accion', 'formatter' => 'clickFormatter']
        ];

        $a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_eliminar(this.form)"];

        $a_valores = [];
        $i = 0;
        foreach ($cGrupos as $oGrupo) {
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

        return $data;
    }
}