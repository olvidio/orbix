<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql\traits;

use PDO;
use PDOStatement;
use src\personas\domain\criteria\PersonaField;
use src\personas\domain\PersonaPublicacion;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;

/**
 * Ejecuta un {@see Criteria} sobre la tabla de un repositorio de personas y devuelve las filas ya
 * hidratadas (fechas `f_nacimiento`/`f_situacion`/`f_inc` y, si procede, `publicado_para`), listas para
 * `fromArray()`. Se usa junto a {@see \src\shared\traits\HandlesPdoErrors}.
 *
 * Lee de `getoDbl()` (como los `getPersonas` antiguos): estos repositorios no fijan conexión de selección.
 */
trait PersonaCriteriaTrait
{
    abstract public function getoDbl(): PDO;

    abstract public function getNomTabla(): string;

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    abstract protected function normalizeAssocRow(array $row): array;

    /**
     * @param array<int|string, mixed> $params
     */
    abstract protected function prepareAndExecute(PDO $db, string $sql, array $params, string $errorKey, string $file, int $line): PDOStatement|false;

    /**
     * @param string|null $condicionExtra Condición SQL fija (sin parámetros) que se aplica antes del criterio.
     * @return list<array<string, mixed>>
     */
    protected function filasPersonasMatching(Criteria $criteria, ?string $condicionExtra = null, bool $conPublicacion = true): array
    {
        $q = (new PgCriteriaConverter(PersonaField::class, []))->convert($criteria);
        $origen = $this->getNomTabla();
        if ($condicionExtra !== null) {
            $origen = "(SELECT * FROM $origen WHERE $condicionExtra) AS personas_filtradas";
        }
        $stmt = $this->prepareAndExecute($this->getoDbl(), "SELECT * FROM $origen" . $q['sql'], $q['params'], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos = $this->normalizeAssocRow($aDatos);
            // para las fechas del postgres (texto iso)
            $aDatos['f_nacimiento'] = (new ConverterDate('date', $aDatos['f_nacimiento']))->fromPg();
            $aDatos['f_situacion'] = (new ConverterDate('date', $aDatos['f_situacion']))->fromPg();
            $aDatos['f_inc'] = (new ConverterDate('date', $aDatos['f_inc']))->fromPg();
            if ($conPublicacion) {
                $aDatos = PersonaPublicacion::hydrateRow($aDatos);
            }
            $filas[] = $aDatos;
        }

        return $filas;
    }
}
