<?php

namespace src\misas\application;

use RuntimeException;
use src\misas\application\support\IdNomJefeResolver;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\security\HashB;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Formulario "Cambiar estado del plan de misas" (zona, estado, orden).
 */
class CambiarStatusPantallaData
{

    public function __construct(
        private readonly ZonaRepositoryInterface $zonaRepository,
        private readonly IdNomJefeResolver $idNomJefeResolver,
    ) {
    }
    /**
     * @return array{
     *   zonas_opciones: array<int|string, string>,
     *   orden_opciones: array<string, string>,
     *   estados_opciones: array<int, string>
     * }
     */
    /**
     * @return array<string, mixed>
     */

    public function getData(): array
    {
        $jefe = $this->idNomJefeResolver->resolve();
        if ($jefe['error'] !== '') {
            throw new RuntimeException($jefe['error']);
        }
        $id_nom_jefe = $jefe['id_nom_jefe'];
        $zonas = $this->zonaRepository->getArrayZonas($id_nom_jefe);

        // Una cápsula por zona permitida para este jefe: `nuevo_status` solo
        // acepta un id_zona atado a una de estas cápsulas, no cualquier
        // id_zona que el cliente decida enviar (getArrayZonas ya filtra por
        // permiso, pero NuevoStatusPeriodo no vuelve a comprobarlo).
        $zona_ctx_map = [];
        foreach (array_keys($zonas) as $id_zona) {
            $zona_ctx_map[(string)$id_zona] = HashB::sign('nuevo_status', ['id_zona' => (int)$id_zona]);
        }

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
            'zona_ctx_map' => $zona_ctx_map,
        ];
    }
}
