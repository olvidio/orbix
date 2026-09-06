<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\copias;

use PDO;
use src\shared\domain\copias\DefinicionCopia;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\StoresPdoErrorTxt;

/**
 * Escrituras y lecturas sobre una tabla copia, para una conexión/esquema concretos.
 *
 * A diferencia de un repositorio normal —que fija su conexión en el constructor
 * y sirve a la aplicación web— aquí la conexión llega en el {@see ContextoCopia},
 * para que la reconciliación multi-esquema pueda usar la conexión de
 * mantenimiento sobre cualquier esquema.
 *
 * El upsert es UPDATE y, si no toca ninguna fila, INSERT. No se usa `ON CONFLICT`
 * a propósito: la clave primaria de las tablas copia no es la misma en todas las
 * instalaciones (hay esquemas con `id_schema` en la pkey, y en cargos hay
 * esquemas con pkey en `(id_activ, id_cargo)`), y el destino de un `ON CONFLICT`
 * tiene que coincidir con un índice único existente.
 */
class CopiaWriter
{
    use HandlesPdoErrors;
    use StoresPdoErrorTxt;

    /** Tope de claves por sentencia DELETE, para no pasarse de marcadores. */
    private const LOTE_BORRADO = 500;

    public function __construct(
        protected readonly DefinicionCopia $definicion,
    ) {
    }

    public function getErrorTxt(): string
    {
        return $this->sErrorTxt;
    }

    /** Qué columnas se copian y cómo se comparan; lo necesita el diff de la reconciliación. */
    public function definicion(): DefinicionCopia
    {
        return $this->definicion;
    }

    /**
     * Inserta o actualiza la fila. Devuelve false si falla el SQL.
     *
     * @param array<string, mixed> $fila
     */
    public function upsert(ContextoCopia $contexto, array $fila): bool
    {
        $definicion = $this->definicion;
        $fila = $definicion->paraEscribir($fila);
        $clave = $definicion->valorClave($fila);
        if ($clave === 0) {
            $this->setErrorTxt(sprintf('%s: %s no válido', $definicion->tabla, $definicion->clave));

            return false;
        }

        $tabla = $contexto->tabla();
        $pdo = $contexto->pdoComun;

        $asignaciones = array_map(
            static fn(string $columna): string => $columna . ' = :' . $columna,
            $definicion->columnasActualizables(),
        );
        $sql = "UPDATE $tabla SET " . implode(', ', $asignaciones)
            . ' WHERE ' . $definicion->clave . ' = :' . $definicion->clave;
        $stmt = $this->pdoPrepare($pdo, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        if ($this->pdoExecute($stmt, $fila, __METHOD__, __FILE__, __LINE__) === false) {
            return false;
        }
        if ($stmt->rowCount() > 0) {
            return true;
        }

        // No existía: INSERT.
        $columnas = $definicion->columnas;
        $valores = array_map(static fn(string $c): string => ':' . $c, $columnas);
        $datos = $fila;
        if ($contexto->id_schema > 0) {
            array_unshift($columnas, 'id_schema');
            array_unshift($valores, ':id_schema');
            $datos['id_schema'] = $contexto->id_schema;
        }
        $sql = "INSERT INTO $tabla (" . implode(',', $columnas) . ') VALUES (' . implode(',', $valores) . ')';
        $stmt = $this->pdoPrepare($pdo, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $datos, __METHOD__, __FILE__, __LINE__);
    }

    public function eliminar(ContextoCopia $contexto, int $clave): bool
    {
        if ($clave === 0) {
            return false;
        }

        return $this->eliminarVarios($contexto, [$clave]) !== false;
    }

    /**
     * @param list<int> $claves
     * @return int|false número de filas borradas, o false si falla el SQL
     */
    public function eliminarVarios(ContextoCopia $contexto, array $claves): int|false
    {
        $claves = array_values(array_filter(
            array_map('intval', $claves),
            static fn(int $clave): bool => $clave !== 0,
        ));
        if ($claves === []) {
            return 0;
        }

        $tabla = $contexto->tabla();
        $borradas = 0;
        foreach (array_chunk($claves, self::LOTE_BORRADO) as $lote) {
            $marcadores = [];
            $datos = [];
            foreach ($lote as $i => $clave) {
                $marcadores[] = ':id' . $i;
                $datos['id' . $i] = $clave;
            }
            $sql = "DELETE FROM $tabla WHERE " . $this->definicion->clave
                . ' IN (' . implode(',', $marcadores) . ')';
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
     * @return array<int, array<string, mixed>>|false clave => fila
     */
    public function filasActuales(ContextoCopia $contexto): array|false
    {
        $tabla = $contexto->tabla();
        $columnas = implode(',', $this->definicion->columnas);
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
            $fila = $this->definicion->desdeRegistro(self::clavesTexto($registro));
            $clave = $this->definicion->valorClave($fila);
            if ($clave !== 0) {
                $filas[$clave] = $fila;
            }
        }

        return $filas;
    }

    /**
     * Claves de filas cuyo `id_schema` no es el del contexto.
     *
     * Cada tabla copia se crea una vez por esquema de comun, así que en
     * condiciones normales esto está vacío. Si no lo está, la instalación tiene
     * la copia compartida entre esquemas y la reconciliación no debe borrar esas
     * filas: pertenecen a otra dl.
     *
     * @return list<int>
     */
    public function clavesDeOtroEsquema(ContextoCopia $contexto): array
    {
        if ($contexto->id_schema <= 0) {
            return [];
        }

        $tabla = $contexto->tabla();
        $sql = 'SELECT ' . $this->definicion->clave . " FROM $tabla"
            . ' WHERE id_schema IS NOT NULL AND id_schema <> :id_schema';
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

        $claves = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $clave) {
            if (is_numeric($clave)) {
                $claves[] = (int) $clave;
            }
        }

        return $claves;
    }

    /**
     * PDO puede devolver claves de tipo array-key; las columnas se leen por nombre.
     *
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
}
