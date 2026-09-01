<?php

declare(strict_types=1);

namespace src\actividadcargos\domain;

use src\actividadcargos\domain\entity\ActividadCargo;

/**
 * Definición de la copia `cd_cargos_activ_dl` (BD comun): qué columnas se copian
 * y cómo se compara una fila de origen con la de destino.
 *
 * `cd_cargos_activ_dl` es una copia de `d_cargos_activ_dl` (BD sv-e) que vive
 * en la BD **comun**, para que las instalaciones sin acceso a sv-e (sf, DMZ)
 * puedan resolver los cargos de una actividad — en particular los sacd
 * ({@see \src\actividadcargos\infrastructure\persistence\postgresql\PgActividadCargoDlRepository::getActividadSacds}).
 *
 * Origen (BD sv-e): `<esquema>v.d_cargos_activ_dl`.
 */
final class CdCargosActivFila
{
    /** Columnas que se copian a `cd_cargos_activ_dl`, en el orden de los INSERT. */
    public const COLUMNAS = [
        'id_item',
        'id_activ',
        'id_cargo',
        'id_nom',
        'puede_agd',
        'observ',
    ];

    /**
     * Fila lista para escribir en `cd_cargos_activ_dl` a partir de la entidad.
     *
     * Usa el mismo `toArrayForDatabase()` que el repositorio de origen, de modo
     * que los valores son los que ya se están escribiendo en sv-e.
     *
     * @return array<string, mixed> claves = {@see COLUMNAS}
     */
    public static function desdeCargo(ActividadCargo $cargo): array
    {
        return self::desdeRegistro($cargo->toArrayForDatabase());
    }

    /**
     * Fila lista para escribir a partir de un registro leído de la BD.
     *
     * @param array<string, mixed> $registro
     * @return array<string, mixed>
     */
    public static function desdeRegistro(array $registro): array
    {
        $fila = [];
        foreach (self::COLUMNAS as $columna) {
            $fila[$columna] = $registro[$columna] ?? null;
        }

        return $fila;
    }

    /**
     * id_item de una fila, 0 si no es utilizable.
     *
     * @param array<string, mixed> $fila
     */
    public static function idItem(array $fila): int
    {
        $valor = $fila['id_item'] ?? null;

        return is_numeric($valor) && (int) $valor > 0 ? (int) $valor : 0;
    }

    /**
     * Toda fila con `id_item` válido debe estar en la copia: es un espejo 1:1
     * de `d_cargos_activ_dl`.
     *
     * @param array<string, mixed> $fila
     */
    public static function debeCopiarse(array $fila): bool
    {
        return self::idItem($fila) > 0;
    }

    /**
     * Forma canónica para comparar origen y destino sin falsos positivos
     * (null vs '', bool vs 't', int vs '3').
     *
     * @param array<string, mixed> $fila
     * @return array<string, string>
     */
    public static function normalizar(array $fila): array
    {
        $normalizada = [];
        foreach (self::COLUMNAS as $columna) {
            $valor = $fila[$columna] ?? null;
            if ($columna === 'puede_agd') {
                $normalizada[$columna] = self::esVerdadero($valor) ? 't' : 'f';
                continue;
            }
            $normalizada[$columna] = self::aTexto($valor);
        }

        return $normalizada;
    }

    /**
     * Columnas cuyo valor difiere entre origen y destino (para el informe).
     *
     * @param array<string, mixed> $origen
     * @param array<string, mixed> $destino
     * @return list<string>
     */
    public static function diferencias(array $origen, array $destino): array
    {
        $a = self::normalizar($origen);
        $b = self::normalizar($destino);

        $distintas = [];
        foreach (self::COLUMNAS as $columna) {
            if ($a[$columna] !== $b[$columna]) {
                $distintas[] = $columna;
            }
        }

        return $distintas;
    }

    private static function aTexto(mixed $valor): string
    {
        if ($valor === null) {
            return '';
        }
        if (is_bool($valor)) {
            return $valor ? 't' : 'f';
        }
        if (is_scalar($valor)) {
            return trim((string) $valor);
        }

        return trim((string) json_encode($valor));
    }

    public static function esVerdadero(mixed $valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }
        if (is_int($valor)) {
            return $valor === 1;
        }
        if (is_string($valor)) {
            return in_array(strtolower(trim($valor)), ['t', 'true', '1', 'y', 'yes', 'si'], true);
        }

        return false;
    }
}
