<?php

declare(strict_types=1);

namespace src\notas\infrastructure\persistence\postgresql\traits;

use PDO;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;

/**
 * Ejecuta un {@see Criteria} con la conexión de escritura del repositorio (`getoDbl()`), que es
 * la que usaban los antiguos `getXxx(array, array)` de los repos de notas (varios la sustituyen
 * en el constructor de la subclase). Se usa junto a `HandlesPdoErrors`.
 */
trait NotasCriteriaTrait
{
    /**
     * @param class-string<\src\shared\domain\criteria\CriteriaField> $campos enum de campos del repositorio
     * @param string $columnas lista de columnas del SELECT
     * @param string|null $condicionExtra condición SQL fija (sin parámetros) que se aplica antes del criterio
     * @return list<array<string, mixed>>
     */
    protected function filasNotasMatching(Criteria $criteria, string $campos, string $columnas = '*', ?string $condicionExtra = null): array
    {
        $q = (new PgCriteriaConverter($campos, []))->convert($criteria);
        $origen = $this->getNomTabla();
        if ($condicionExtra !== null && $condicionExtra !== '') {
            $origen = "(SELECT * FROM $origen WHERE $condicionExtra) AS notas_filtradas";
        }
        $stmt = $this->prepareAndExecute($this->getoDbl(), "SELECT $columnas FROM $origen" . $q['sql'], $q['params'], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $filas[] = $normalized;
        }

        return $filas;
    }
}
