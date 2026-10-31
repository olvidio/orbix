<?php

namespace Tests\unit\notas;

use src\notas\domain\criteria\PersonaNotaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Búsqueda de notas por igualdad de campos con `matching()` (sustituye al antiguo `getPersonaNotas`).
 */
trait BuscaPersonaNotasTrait
{
    /**
     * @param object $repo repositorio de notas con `matching(Criteria)`
     * @param array<string, mixed> $campos nombre de campo => valor (solo igualdad)
     * @return array<int, object>
     */
    private function buscarPersonaNotas(object $repo, array $campos): array
    {
        $criteria = Criteria::create();
        foreach ($campos as $campo => $valor) {
            $criteria = $criteria->andWhere(Filter::eq(PersonaNotaField::from($campo), $valor));
        }

        return $repo->matching($criteria);
    }
}
