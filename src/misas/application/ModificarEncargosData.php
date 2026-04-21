<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarEncargosData
{
    /**
     * Devuelve los datos para pintar la pantalla `modificar_encargos`:
     * el desplegable de zonas (filtrado segun el rol del usuario) y la lista
     * de criterios de orden aceptados por el grid.
     *
     * Replica la logica de `apps/misas/controller/modificar_encargos.php`:
     * si el rol es `p-sacd` y NO es jefe de calendario, se limitan las
     * zonas a las del `id_pau` del propio usuario.
     *
     * Devuelve:
     *   - `error`          : texto vacio si todo ok, mensaje si el usuario
     *                         no tiene permiso para ver la pantalla.
     *   - `a_opciones_zona`: array id_zona => nombre_zona.
     *   - `a_orden`        : array criterio => label.
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
                        'a_orden' => [],
                    ];
                }
            }
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $a_opciones_zona = $ZonaRepository->getArrayZonas($id_nom_jefe);

        $a_orden = [
            'orden' => _('orden'),
            'prioridad' => _('prioridad'),
            'desc_enc' => _('alfabético'),
        ];

        return [
            'error' => '',
            'a_opciones_zona' => $a_opciones_zona,
            'a_orden' => $a_orden,
        ];
    }
}
