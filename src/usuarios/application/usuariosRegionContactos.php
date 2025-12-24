<?php

namespace src\usuarios\application;

use core\ConfigDB;
use core\DBConnection;
use permisos\model\PermDl;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\ContestarJson;

class usuariosRegionContactos
{

    /**
     * @param string $region
     * @return array
     */
    public static function usuariosRegionContactos(string $region = '')
    {
        $error_txt = '';
        $esquema = $region."v";
        $oConfigDB = new ConfigDB('sv-e_select');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        try {
            $oDevelPC = $oConexion->getPDO();
        } catch (\Throwable $e) {
            $error_txt = 'Error al obtener la conexión a la base de datos: ' . $e->getMessage();
            ContestarJson::enviar($error_txt, '');
            return;
        }

        // todos los usuarios de la region, y después mirar los permisos
        $UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $UsuarioGrupoRepository->setoDbl_Select($oDevelPC);
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $UsuarioRepository->setoDbl_select($oDevelPC);
        $cUsuariosRegion = $UsuarioRepository->getUsuarios(['id_role' => 3],['id_role' => '>']); // quitar los admin.

        $aContactos = [];
        foreach ($cUsuariosRegion as $oUsuario) {
            $email = $oUsuario->getEmailAsString();
            if (empty($email)) {
                continue;
            }
            $id_usuario = $oUsuario->getId_usuario();
            $usuario = $oUsuario->getUsuarioAsString();
            $nom_usuario = $oUsuario->getNom_usuarioAsString()?? $usuario;

            // tiene permiso de est?
            $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => $id_usuario));
            $iperm_menu = 0;
            $PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
            $PermMenuRepository->setoDbl_Select($oDevelPC);
            foreach ($cGrupos as $UsuarioGrupo) {
                $id_grupo = $UsuarioGrupo->getId_grupo();
                $cPermMenu = $PermMenuRepository->getPermMenus(array('id_usuario' => $id_grupo));
                foreach ($cPermMenu as $oPermMenu) {
                    // Or (inclusive or) 	Bits that are set in either $a or $b are set.
                    $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
                }
            }
            // añadir el permiso de 'jefeZona'
            //echo "perms: $iperm_menu<br>";
            $_SESSION['iPermMenus'] = $iperm_menu;
            $oPerm = new PermDl();
            $oPerm->setAccion($iperm_menu);

            if ($oPerm->have_perm_oficina('est') === true
                || $oPerm->have_perm_oficina('sm') === true
                || $oPerm->have_perm_oficina('agd') === true)
            {
                $aContactos[$nom_usuario] = ['email' => $email, 'cargo' => $oPerm->lista_txt2($iperm_menu)];
            }
        }

        /*
        $data = ['success' => true,
            'contactos' => [
                'dani' => ['cargo' => 'vsm', 'email' => 'algo@moneders.net'],
                'jordi' => ['cargo' => 'vest', 'email' => 'jordi@moneders.net'],
            ]
        ];
        */

        $data = ['success' => true,
            'contactos' => $aContactos
        ];

        // envía una Response
        ContestarJson::enviar($error_txt, $data);
    }
}