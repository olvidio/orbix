<?php

namespace src\usuarios\application;

use Exception;
use InvalidArgumentException;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;

class usuariosLista
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(string $Qusername = ''): array
    {
        $error_txt = '';

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ContestarJson::respuestaPhp(_('Usuario no encontrado'), '');
        }
        $miRole = $oMiUsuario->getId_role();

        if ($miRole > 3) {
            $error_txt = _('no tiene permisos para ver esto');

            return ContestarJson::respuestaPhp($error_txt, '');
        }

        $miSfsv = ConfigGlobal::mi_sfsv();
        $aWhere = [];
        $aOperador = [];
        if ($miRole !== 1) {
            $aWhere['id_role'] = 1;
            $aOperador['id_role'] = '!=';
        }

        if ($Qusername !== '') {
            $aWhere['usuario'] = $Qusername;
            $aOperador['usuario'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'usuario';

        try {
            $cUsuarios = $this->usuarioRepository->getUsuarios($aWhere, $aOperador);
        } catch (InvalidArgumentException $e) {
            $error_txt .= _('Error (no debería ocurrir): Se capturó una InvalidArgumentException: ');
            $error_txt .= $e->getMessage() . "\n\n";

            return ContestarJson::respuestaPhp($error_txt, '');
        } catch (Exception $e) {
            $error_txt .= _('Error: Se capturó una excepción genérica: ');
            $error_txt .= $e->getMessage() . "\n\n";

            return ContestarJson::respuestaPhp($error_txt, '');
        }

        $a_cabeceras = ['usuario', 'nombre a mostrar', 'role', 'email', ['name' => 'accion', 'formatter' => 'clickFormatter']];
        $a_botones[] = ['txt' => _('borrar'), 'click' => 'fnjs_eliminar()'];

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
                $oRole = $this->roleRepository->findById($id_role);
                if ($oRole !== null) {
                    $role = $oRole->getRoleAsString() ?? '?';
                    if ($miSfsv === 1 && !$oRole->isSv()) {
                        continue;
                    }
                    if ($miSfsv === 2 && !$oRole->isSf()) {
                        continue;
                    }
                }
            }

            $a_valores[$i]['sel'] = "$id_usuario#";
            $a_valores[$i][1] = $usuario;
            $a_valores[$i][2] = $nom_usuario;
            $a_valores[$i][3] = $role;
            $a_valores[$i][5] = $email;
            $a_valores[$i][6] = [
                'link_spec' => [
                    'path' => 'frontend/usuarios/controller/usuario_form.php',
                    'query' => ['quien' => 'usuario', 'id_usuario' => $id_usuario],
                ],
                'valor' => 'editar',
            ];
        }

        $data = [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}
