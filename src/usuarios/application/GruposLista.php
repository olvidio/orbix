<?php

namespace src\usuarios\application;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;

class GruposLista
{

    public function __invoke($username): array
    {
        $aWhere = [];
        $aOperador = [];
        if (!empty($username)) {
            $aWhere['usuario'] = $username;
            $aOperador['usuario'] = 'sin_acentos';
        } else {
            $aWhere['id_usuario'] = '^5';
            $aOperador['id_usuario'] = '~';
        }
        $aWhere['_ordre'] = 'usuario';

        $GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
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
            $usuario = $oGrupo->getUsuarioAsString();

            // Enlace hacia frontend: solo datos; la firma HashF se hace en grupo_lista.php (capa UI).
            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = [
                'link_spec' => [
                    'path' => 'frontend/usuarios/controller/grupo_form.php',
                    'query' => ['quien' => 'grupo', 'id_usuario' => $id_usuario],
                ],
                'valor' => 'editar',
            ];
        }

        $data = ['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return $data;
    }
}