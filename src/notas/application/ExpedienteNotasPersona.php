<?php

namespace src\notas\application;

use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\domain\value_objects\TipoActa;

/**
 * Expediente de notas de un alumno vía la tabla padre `publicv.e_notas`
 * (herencia de `{esquema}.e_notas_dl`). Ver docs/dev/notas_modelo_acta.md.
 *
 * Si coexisten fila de acta (`tipo_acta=1`) y de certificado (`tipo_acta=2`)
 * para la misma asignatura, prevalece la de acta. Una nota solo-certificado
 * (calificación recibida de entidad externa, sin acta en Orbix) se conserva.
 */
final class ExpedienteNotasPersona
{
    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    /**
     * @return list<PersonaNota|PersonaNotaOtraRegionStgr>
     */
    public function getNotas(int $id_nom): array
    {
        $cNotas = $this->personaNotaRepository->getPersonaNotas(
            ['id_nom' => $id_nom, '_ordre' => 'id_nivel'],
            ['id_asignatura' => '<']
        );

        return $this->preferirActaSobreCertificado($cNotas);
    }

    /**
     * @param list<PersonaNota|PersonaNotaOtraRegionStgr> $cNotas
     * @return list<PersonaNota|PersonaNotaOtraRegionStgr>
     */
    private function preferirActaSobreCertificado(array $cNotas): array
    {
        /** @var array<string, PersonaNota|PersonaNotaOtraRegionStgr> $porAsignatura */
        $porAsignatura = [];
        foreach ($cNotas as $oNota) {
            $key = (string) $oNota->getId_asignatura();
            $tipo = $oNota->getTipo_acta() ?? TipoActa::FORMATO_ACTA;
            if (!isset($porAsignatura[$key])) {
                $porAsignatura[$key] = $oNota;
                continue;
            }
            $actualTipo = $porAsignatura[$key]->getTipo_acta() ?? TipoActa::FORMATO_ACTA;
            if ($tipo === TipoActa::FORMATO_ACTA && $actualTipo === TipoActa::FORMATO_CERTIFICADO) {
                $porAsignatura[$key] = $oNota;
            }
        }

        return array_values($porAsignatura);
    }
}
