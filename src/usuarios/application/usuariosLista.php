<?php

namespace src\usuarios\application;

use core\ConfigGlobal;
use Exception;
use InvalidArgumentException;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
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

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $miRole = $oMiUsuario->getId_role();

        if ($miRole > 3) {
            // no es administrador
            $error_txt = _("no tiene permisos para ver esto");
            return ContestarJson::respuestaPhp($error_txt, '');
        }

        $miSfsv = ConfigGlobal::mi_sfsv();
        $aWhere = [];
        $aOperador = [];
        if ($miRole !== 1) { // id_role=1 => SuperAdmin.
            $aWhere['id_role'] = 1;
            $aOperador['id_role'] = '!='; // para no tocar al administrador
        }

        if (!empty($Qusername)) {
            $aWhere['usuario'] = $Qusername;
            $aOperador['usuario'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'usuario';

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
         try {
             $cUsuarios = $UsuarioRepository->getUsuarios($aWhere, $aOperador);
        } catch (InvalidArgumentException $e) {
            $error_txt .= _("Error (no debería ocurrir): Se capturó una InvalidArgumentException: ");
            $error_txt .=  $e->getMessage() . "\n\n";
             return ContestarJson::respuestaPhp($error_txt, '');
        } catch (Exception $e) {
            $error_txt .= _("Error: Se capturó una excepción genérica: ");
            $error_txt .= $e->getMessage() . "\n\n";
             return ContestarJson::respuestaPhp($error_txt, '');
        }

        $a_cabeceras = array('usuario', 'nombre a mostrar', 'role', 'email', array('name' => 'accion', 'formatter' => 'clickFormatter'));
        $a_botones[] = array('txt' => _("borrar"), 'click' => "fnjs_eliminar()");

        $a_valores = [];
        $i = 0;
        foreach ($cUsuarios as $oUsuario) {
            $i++;
            $id_usuario = $oUsuario->getId_usuario();
            $usuario = $oUsuario->getUsuarioAsString();
            $nom_usuario = $oUsuario->getNomUsuarioAsString();
            $email = $oUsuario->getEmailAsString();
            $id_role = $oUsuario->getId_role();

            $role = '?';
            if (!empty($id_role)) {
                // Cuando se ha eliminado el Role, el usuario todavía tiene el id, pero no existe:
                $oRole = $RoleRepository->findById($id_role);
                if ($oRole !== null) {
                    $role = $oRole->getRoleAsString();
                    // filtro por sf/sv
                    if ($miSfsv === 1 && !$oRole->isSv()) {
                        continue;
                    }
                    if ($miSfsv === 2 && !$oRole->isSf()) {
                        continue;
                    }
                }
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

        $data = ['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}