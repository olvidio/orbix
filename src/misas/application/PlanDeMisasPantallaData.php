<?php

namespace src\misas\application;

use core\ConfigGlobal;
use RuntimeException;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\domain\value_objects\PlantillaConfig;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
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

        $jefe = IdNomJefeResolver::resolve();
        if ($jefe['error'] !== '') {
            throw new RuntimeException($jefe['error']);
        }
        $id_nom_jefe = $jefe['id_nom_jefe'];

        $container = $GLOBALS['container'];
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
