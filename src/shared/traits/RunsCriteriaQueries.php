<?php

declare(strict_types=1);

namespace src\shared\traits;

use PDO;
use PDOStatement;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;

/**
 * Ejecuta un {@see Criteria} contra la tabla del repositorio y devuelve las filas, para que
 * cada `matching()` solo tenga que hidratar. Se usa junto a {@see HandlesPdoErrors}, que
 * aporta el manejo de errores PDO; las conexiones y la tabla las da `ClaseRepository`.
 *
 * Uso en un repositorio:
 *
 *     public function matching(Criteria $criteria): array
 *     {
 *         $conv = new PgCriteriaConverter(AsistenteField::class, self::COLUMNAS);
 *         return array_map(Asistente::fromArray(...), $this->rowsMatching($criteria, $conv));
 *     }
 */
trait RunsCriteriaQueries
{
    abstract public function getoDbl_Select(): PDO;

    abstract public function getNomTabla(): string;

    /**
     * @param array<int|string, mixed> $params
     */
    abstract protected function prepareAndExecute(PDO $db, string $sql, array $params, string $errorKey, string $file, int $line): PDOStatement|false;

    /**
     * Filas (`SELECT *`) que cumplen el criterio, con su orden, límite y desplazamiento.
     *
     * @return list<array<string, mixed>>
     */
    protected function rowsMatching(Criteria $criteria, PgCriteriaConverter $converter): array
    {
        $q = $converter->convert($criteria);
        return $this->fetchRows('SELECT * FROM ' . $this->getNomTabla() . $q['sql'], $q['params'], __METHOD__);
    }

    /** Número de filas que cumplen los filtros del criterio (ignora orden y límite). */
    protected function countMatching(Criteria $criteria, PgCriteriaConverter $converter): int
    {
        $q = $converter->convertWhere($criteria);
        $rows = $this->fetchRows('SELECT count(*) AS n FROM ' . $this->getNomTabla() . $q['sql'], $q['params'], __METHOD__);
        return (int) ($rows[0]['n'] ?? 0);
    }

    /**
     * @param array<string, mixed> $params
     * @return list<array<string, mixed>>
     */
    private function fetchRows(string $sql, array $params, string $errorKey): array
    {
        $stmt = $this->prepareAndExecute($this->getoDbl_Select(), $sql, $params, $errorKey, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $rows = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $rows[] = $fila;
        }
        return $rows;
    }
}
