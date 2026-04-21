<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\misas\domain\value_objects\PlantillaConfig;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Datos comunes para las pantallas preparar / modificar / ver plan de misas
 * y para modificar plantilla (mismos desplegables de zona / tipo / orden).
 */
class PlanDeMisasPantallaData
{
    /**
     * @return array{
     *   pantalla: string,
     *   zonas_opciones: array<int|string, string>,
     *   orden_opciones: array<string, string>,
     *   tipos_plantilla?: array<int, string>,
     *   plantilla_selected?: int
     * }
     */
    public static function getData(string $pantalla): array
    {
        if (!in_array($pantalla, ['preparar', 'modificar', 'ver', 'modificar_plantilla'], true)) {
            $pantalla = 'preparar';
        }

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

        $out = [
            'pantalla' => $pantalla,
            'zonas_opciones' => $zonas,
            'orden_opciones' => $orden,
        ];

        if ($pantalla === 'preparar' || $pantalla === 'modificar_plantilla') {
            $tipos = [
                PlantillaConfig::PLANTILLA_SEMANAL_UNO => 'semanal una opción',
                PlantillaConfig::PLANTILLA_DOMINGOS_UNO => 'semanal y domingos una opción',
                PlantillaConfig::PLANTILLA_MENSUAL_UNO => 'mensual una opción',
                PlantillaConfig::PLANTILLA_SEMANAL_TRES => 'semanal tres opciones',
                PlantillaConfig::PLANTILLA_DOMINGOS_TRES => 'semanal y domingos tres opciones',
                PlantillaConfig::PLANTILLA_MENSUAL_TRES => 'mensual tres opciones',
            ];
            $PreferenciaRepository = $container->get(PreferenciaRepositoryInterface::class);
            $id_usuario = ConfigGlobal::mi_id_usuario();
            $aPref = $PreferenciaRepository->getPreferencias(['id_usuario' => $id_usuario, 'tipo' => 'ultima_plantilla']);
            $ultima = PlantillaConfig::PLANTILLA_SEMANAL_TRES;
            if (count($aPref) > 0) {
                $ultima = (int)$aPref[0]->getPreferencia();
            }
            $out['tipos_plantilla'] = $tipos;
            $out['plantilla_selected'] = $ultima;
        }

        return $out;
    }
}
