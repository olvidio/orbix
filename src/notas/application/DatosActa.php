<?php

namespace src\notas\application;

use src\notas\domain\criteria\PersonaNotaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;

/**
 * Notas asociadas a un acta concreta (formato acta) para impresión y hash.
 * Incluye alumnos con nota; no incluye matrículas sin calificar.
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
     * @return list<PersonaNota>
     */
    public function getNotasActa(string $acta): array
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(F::Acta, $acta))
            ->andWhere(Filter::eq(F::TipoActa, TipoActa::FORMATO_ACTA));

        $out = [];
        foreach ($this->personaNotaRepository->matching($criteria) as $oPersonaNota) {
            if (self::entraEnImpresion($oPersonaNota)) {
                $out[] = $oPersonaNota;
            }
        }

        return $out;
    }

    public static function entraEnImpresion(PersonaNota $oPersonaNota): bool
    {
        $num = $oPersonaNota->getNota_num();
        if ($num !== null && trim($num) !== '') {
            return true;
        }

        return in_array($oPersonaNota->getId_situacion(), NotaSituacion::getArraySuperadas(), true);
    }
}
