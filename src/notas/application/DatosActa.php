<?php

namespace src\notas\application;

use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;

/**
 * Pequeno servicio consultado por las impresiones de actas
 * (`acta_imprimir`, `acta_imprimir_mpdf`, PDF) para obtener las
 * `PersonaNota` superadas asociadas a un acta concreta en formato
 * `FORMATO_ACTA`.
 *
 * Migrado desde `apps/notas/model/getDatosActa`.
 */
final class DatosActa
{
    public function __construct(
        private readonly PersonaNotaRepositoryInterface $personaNotaRepository,
    ) {
    }

    /**
     * @return \src\notas\domain\entity\PersonaNota[]
     */
    public function getNotasActa(string $acta): array
    {
        $aIdSuperadas = NotaSituacion::getArraySuperadas();

        $aWhere = [
            'id_situacion' => implode(',', $aIdSuperadas),
            'acta' => $acta,
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ];
        $aOperador = ['id_situacion' => 'IN'];

        return $this->personaNotaRepository->getPersonaNotas($aWhere, $aOperador);
    }
}
