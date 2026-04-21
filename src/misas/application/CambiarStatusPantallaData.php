<?php

namespace src\misas\application;

use RuntimeException;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\domain\value_objects\EncargoDiaStatus;
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
        $jefe = IdNomJefeResolver::resolve();
        if ($jefe['error'] !== '') {
            throw new RuntimeException($jefe['error']);
        }
        $id_nom_jefe = $jefe['id_nom_jefe'];

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
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
