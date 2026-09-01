<?php

declare(strict_types=1);

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use PDO;
use src\actividadcargos\domain\CdCargosActivFila;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\StoresPdoErrorTxt;

/**
 * Escrituras y lecturas sobre `cd_cargos_activ_dl` para una conexión/esquema concretos.
 *
 * El upsert es UPDATE por `id_item` y, si no toca ninguna fila, INSERT. No se
 * usa `ON CONFLICT` a propósito: la clave primaria de `cd_cargos_activ_dl` no
 * es la misma en todas las instalaciones (hay esquemas con pkey en
 * `(id_activ, id_cargo)` y otros en `id_item`) y el destino de un ON CONFLICT
 * tiene que coincidir con un índice único existente.
 */
class CdCargosActivWriter
{
    use HandlesPdoErrors;
    use StoresPdoErrorTxt;

    public function getErrorTxt(): string
    {
        return $this->sErrorTxt;
    }

    /**
     * Inserta o actualiza la fila. Devuelve false si falla el SQL.
     *
     * @param array<string, mixed> $fila
     */
    public function upsert(CdCargosActivContexto $contexto, array $fila): bool
    {
        $fila = CdCargosActivFila::desdeRegistro($fila);
        // PDO bindea los bool como '1'/'' y '' no es un boolean válido en Postgres.
        $fila['puede_agd'] = CdCargosActivFila::esVerdadero($fila['puede_agd']) ? 't' : 'f';
        $id_item = CdCargosActivFila::idItem($fila);
        if ($id_item === 0) {
            $this->setErrorTxt('cd_cargos_activ_dl: id_item no válido');

            return false;
        }

        $tabla = $contexto->tabla();
        $oDbl = $contexto->pdoComun;

        $asignaciones = [];
        foreach (CdCargosActivFila::COLUMNAS as $columna) {
            if ($columna === 'id_item') {
                continue;
            }
            $asignaciones[] = $columna . ' = :' . $columna;
        }
        $sql = "UPDATE $tabla SET " . implode(', ', $asignaciones) . ' WHERE id_item = :id_item';
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        if ($this->pdoExecute($stmt, $fila, __METHOD__, __FILE__, __LINE__) === false) {
            return false;
        }
        if ($stmt->rowCount() > 0) {
            return true;
        }

        $columnas = CdCargosActivFila::COLUMNAS;
        $valores = array_map(static fn(string $c): string => ':' . $c, $columnas);
        $datos = $fila;
        if ($contexto->id_schema > 0) {
            array_unshift($columnas, 'id_schema');
            array_unshift($valores, ':id_schema');
            $datos['id_schema'] = $contexto->id_schema;
        }
        $sql = "INSERT INTO $tabla (" . implode(',', $columnas) . ') VALUES (' . implode(',', $valores) . ')';
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $datos, __METHOD__, __FILE__, __LINE__);
    }

    public function eliminar(CdCargosActivContexto $contexto, int $id_item): bool
    {
        if ($id_item === 0) {
            return false;
        }

        return $this->eliminarVarios($contexto, [$id_item]) !== false;
    }

    /**
     * @param list<int> $ids
     * @return int|false número de filas borradas, o false si falla el SQL
     */
    public function eliminarVarios(CdCargosActivContexto $contexto, array $ids): int|false
    {
        $ids = array_values(array_filter(array_map('intval', $ids), static fn(int $id): bool => $id !== 0));
        if ($ids === []) {
            return 0;
        }

        $tabla = $contexto->tabla();
        $borradas = 0;
        foreach (array_chunk($ids, 500) as $lote) {
            $marcadores = [];
            $datos = [];
            foreach ($lote as $i => $id) {
                $marcadores[] = ':id' . $i;
                $datos['id' . $i] = $id;
            }
            $sql = "DELETE FROM $tabla WHERE id_item IN (" . implode(',', $marcadores) . ')';
            $stmt = $this->prepareAndExecute($contexto->pdoComun, $sql, $datos, __METHOD__, __FILE__, __LINE__);
            if ($stmt === false) {
                return false;
            }
            $borradas += $stmt->rowCount();
        }

        return $borradas;
    }

    /**
     * Contenido actual de la copia, para el diff de la reconciliación.
     *
     * @return array<int, array<string, mixed>>|false id_item => fila
     */
    public function filasActuales(CdCargosActivContexto $contexto): array|false
    {
        $tabla = $contexto->tabla();
        $columnas = implode(',', CdCargosActivFila::COLUMNAS);
        $sql = "SELECT $columnas FROM $tabla";
        $stmt = $this->pdoQuery($contexto->pdoComun, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $filas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $registro) {
            if (!is_array($registro)) {
                continue;
            }
            $fila = CdCargosActivFila::desdeRegistro(self::clavesTexto($registro));
            $id_item = CdCargosActivFila::idItem($fila);
            if ($id_item !== 0) {
                $filas[$id_item] = $fila;
            }
        }

        return $filas;
    }

    /**
     * @param array<array-key, mixed> $registro
     * @return array<string, mixed>
     */
    private static function clavesTexto(array $registro): array
    {
        $normalizado = [];
        foreach ($registro as $clave => $valor) {
            $normalizado[(string) $clave] = $valor;
        }

        return $normalizado;
    }

    /**
     * id_item de filas cuyo `id_schema` no es el del contexto.
     *
     * `cd_cargos_activ_dl` se crea una vez por esquema de comun, así que en
     * condiciones normales esto está vacío. Si no lo está, la instalación
     * tiene la copia compartida entre esquemas y la reconciliación no debe
     * borrar esas filas: pertenecen a otra dl.
     *
     * @return list<int>
     */
    public function idItemsDeOtroEsquema(CdCargosActivContexto $contexto): array
    {
        if ($contexto->id_schema <= 0) {
            return [];
        }

        $tabla = $contexto->tabla();
        $sql = "SELECT id_item FROM $tabla WHERE id_schema IS NOT NULL AND id_schema <> :id_schema";
        $stmt = $this->prepareAndExecute(
            $contexto->pdoComun,
            $sql,
            ['id_schema' => $contexto->id_schema],
            __METHOD__,
            __FILE__,
            __LINE__,
        );
        if ($stmt === false) {
            return [];
        }

        $ids = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) {
            if (is_numeric($id)) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }
}
