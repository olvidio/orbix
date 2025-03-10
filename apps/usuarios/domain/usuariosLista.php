<?php

namespace usuarios\domain;

use core\ConfigGlobal;
use usuarios\model\entity\GestorUsuario;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\ContestarJson;
use web\Hash;

class usuariosLista
{

    /**
     * @param string $Qusername
     * @return array
     */
    public static function usuariosLista(string $Qusername = ''): array
    {
        $error_txt = '';
        $jsondata = [];

        $oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
        $miRole = $oMiUsuario->getId_role();

        if ($miRole > 3) {
            // no es administrador
            $error_txt = _("no tiene permisos para ver esto");
        }


        $miSfsv = ConfigGlobal::mi_sfsv();
        $aWhere = array();
        $aOperador = array();
        if ($miRole !== 1) { // id_role=1 => SuperAdmin.
            $aWhere['id_role'] = 1;
            $aOperador['id_role'] = '!='; // para no tocar al administrador
        }

        if (!empty($Qusername)) {
            $aWhere['usuario'] = $Qusername;
            $aOperador['usuario'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'usuario';

        $oRole = new Role();
        $oGesUsuarios = new GestorUsuario();
        $cUsuarios = $oGesUsuarios->getUsuarios($aWhere, $aOperador);

        $a_cabeceras = array('usuario', 'nombre a mostrar', 'role', 'email', array('name' => 'accion', 'formatter' => 'clickFormatter'));
        $a_botones[] = array('txt' => _("borrar"), 'click' => "fnjs_eliminar()");

        $a_valores = array();
        $i = 0;
        foreach ($cUsuarios as $oUsuario) {
            $i++;
            $id_usuario = $oUsuario->getId_usuario();
            $usuario = $oUsuario->getUsuario();
            $nom_usuario = $oUsuario->getNom_usuario();
            $email = $oUsuario->getEmail();
            $id_role = $oUsuario->getId_role();

            if (!empty($id_role)) {
                // Cuando se ha eliminado el Role, el usuario todavía tiene el id, pero no existe:
                $oRole->setId_role($id_role);
                $oRole->DBCarregar();
                $role = $oRole->getRole();
                // filtro por sf/sv
                if ($miSfsv === 1) {
                    $role_sv = $oRole->isSv();
                    if ($role_sv === FALSE) {
                        continue;
                    }
                }
                if ($miSfsv === 2) {
                    $role_sf = $oRole->isSf();
                    if ($role_sf === FALSE) {
                        continue;
                    }
                }
            } else {
                $role = '?';
            }

            $pagina = Hash::link(ConfigGlobal::getWeb()
                . '/frontend/usuarios/controller/usuario_form.php?'
                . http_build_query(['quien' => 'usuario', 'id_usuario' => $id_usuario])
            );

            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = $nom_usuario;
            $a_valores[$i][3] = $role;
            $a_valores[$i][5] = $email;
            $a_valores[$i][6] = array('ira' => $pagina, 'valor' => 'editar');
        }

        $data =['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}