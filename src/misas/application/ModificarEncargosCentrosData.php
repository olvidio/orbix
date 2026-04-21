<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarEncargosCentrosData
{
    /**
     * Devuelve el desplegable de zonas que el usuario actual puede ver, para
     * pintar la pantalla `modificar_encargos_centros`. Replica la logica de
     * permisos de `apps/misas/controller/modificar_encargos_centros.php`:
     * si el rol es `p-sacd` y NO es jefe de calendario, se limitan las
     * zonas a las del `id_pau` del propio usuario.
     *
     * Devuelve:
     *   - `error`          : texto vacio si todo ok, mensaje si falta permiso.
     *   - `a_opciones_zona`: array id_zona => nombre_zona.
     */
    public static function getData(): array
    {
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $id_nom_jefe = null;
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            if (!$_SESSION['oConfig']->is_jefeCalendario()) {
                $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
                if (empty($id_nom_jefe)) {
                    return [
                        'error' => _('No tiene permiso para ver esta página'),
                        'a_opciones_zona' => [],
                    ];
                }
            }
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);

        return [
            'error' => '',
            'a_opciones_zona' => $ZonaRepository->getArrayZonas($id_nom_jefe),
        ];
    }
}
