<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
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

    public function __construct(
        private readonly ZonaRepositoryInterface $zonaRepository,
        private readonly PreferenciaRepositoryInterface $preferenciaRepository,
        private readonly IdNomJefeResolver $idNomJefeResolver,
    ) {
    }
    /**
     * @return array{
     *   pantalla: string,
     *   zonas_opciones: array<int|string, string>,
     *   orden_opciones: array<string, string>,
     *   tipos_plantilla?: array<string, string>,
     *   plantilla_selected?: string
     * }
     */
    public function getData(string $pantalla): array
    {
        if (!in_array($pantalla, ['preparar', 'modificar', 'ver', 'modificar_plantilla'], true)) {
            $pantalla = 'preparar';
        }

        $jefe = $this->idNomJefeResolver->resolve();
        if ($jefe['error'] !== '') {
            throw new RuntimeException($jefe['error']);
        }
        $id_nom_jefe = $jefe['id_nom_jefe'];
        $zonas = $this->zonaRepository->getArrayZonas($id_nom_jefe);

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
            $id_usuario = ConfigGlobal::mi_id_usuario();
            $aPref = $this->preferenciaRepository->getPreferencias(['id_usuario' => $id_usuario, 'tipo' => 'ultima_plantilla']);
            $ultima = PlantillaConfig::PLANTILLA_SEMANAL_TRES;
            if (count($aPref) > 0) {
                $pref = $aPref[0]->getPreferencia();
                $ultima = is_string($pref) ? $pref : (string) $pref;
            }
            $out['tipos_plantilla'] = $tipos;
            $out['plantilla_selected'] = $ultima;
        }

        return $out;
    }
}
