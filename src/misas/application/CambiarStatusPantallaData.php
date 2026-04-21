<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Formulario "Cambiar estado del plan de misas" (zona, estado, orden).
 */
class CambiarStatusPantallaData
{
    /**
     * @return array{
     *   zonas_opciones: array<int|string, string>,
     *   orden_opciones: array<string, string>,
     *   estados_opciones: array<int, string>
     * }
     */
    public static function getData(): array
    {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $container->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $id_nom_jefe = null;
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            if (!$_SESSION['oConfig']->is_jefeCalendario()) {
                $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
                if ($id_nom_jefe === 0) {
                    exit(_('No tiene permiso para ver esta página'));
                }
            }
        }

        $ZonaRepository = $container->get(ZonaRepositoryInterface::class);
        $zonas = $ZonaRepository->getArrayZonas($id_nom_jefe);

        $orden = [
            'orden' => 'orden',
            'prioridad' => 'prioridad',
            'desc_enc' => 'alfabético',
        ];

        $estados = [
            EncargoDiaStatus::STATUS_PROPUESTA => 'propuesta',
            EncargoDiaStatus::STATUS_COMUNICADO_SACD => 'comunicado sacerdotes',
            EncargoDiaStatus::STATUS_COMUNICADO_CTR => 'comunicado centros',
        ];

        return [
            'zonas_opciones' => $zonas,
            'orden_opciones' => $orden,
            'estados_opciones' => $estados,
        ];
    }
}
