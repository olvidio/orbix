<?php

declare(strict_types=1);

namespace src\ubis\domain;

use src\shared\domain\copias\DefinicionCopia;
use src\shared\domain\copias\ValorCopia;
use src\shared\infrastructure\persistence\ConverterDate;

/**
 * Definición de las copias de centros en la BD comun: `cu_centros_dl` (centros
 * de sv) y `cu_centros_dlf` (centros de sf).
 *
 * Ambas son proyecciones de `u_centros_dl`, que vive en la base **sv** para los
 * centros de sv y en la base **sf** para los de sf. Existen porque la DMZ no
 * tiene `oDB` y porque cada instalación necesita ver los centros de la otra:
 * la sv lee los de sf en `cu_centros_dlf` y viceversa
 * ({@see \src\ubis\application\UbiFactory}).
 *
 * ## Qué se copia
 *
 * 14 de las 24 columnas de `u_centros_dl`. Quedan fuera las que sólo interesan
 * en el interior (`n_buzon`, `num_pi`, `num_cartas`, `observ`,
 * `num_habit_indiv`, `plazas`, `sede`, `num_cartas_mensuales`, `id_auto`) y
 * `id_zona`, que es de la copia (más abajo).
 *
 * ## Reparto entre las dos copias
 *
 * Por el primer dígito de `id_ubi`, que es la misma regla que usan
 * `DBTrasvase::ctr` y `UbiFactory`: los centros de sf empiezan por `2`, los de
 * sv no.
 *
 * ## `id_zona` es del destino, no del origen
 *
 * `id_zona` existe en las dos copias pero **no se copia**. La zona SACD de un
 * centro se asigna en {@see \src\zonassacd\application\ZonaCtrUpdate} desde la
 * instalación sv: para los centros de sv escribe en el origen `u_centros_dl`,
 * pero para los de sf escribe **directamente en `cu_centros_dlf`**, porque sv no
 * puede escribir en la base sf. Reconciliar esa columna desde el origen borraría
 * todas las zonas de los centros de sf, así que se declara del destino en las
 * dos copias (ver {@see DefinicionCopia} y `docs/dev/copias_entre_bases.md`).
 */
final class CuCentrosFila
{
    /** Tabla de origen, en la base sv o sf según la copia. */
    public const TABLA_ORIGEN = 'u_centros_dl';

    public const TABLA_SV = 'cu_centros_dl';
    public const TABLA_SF = 'cu_centros_dlf';

    /** Columnas que se copian desde `u_centros_dl`, en el orden de los INSERT. */
    public const COLUMNAS = [
        'id_ubi',
        'tipo_ubi',
        'nombre_ubi',
        'dl',
        'pais',
        'region',
        'active',
        'f_active',
        'sv',
        'sf',
        'tipo_ctr',
        'tipo_labor',
        'cdc',
        'id_ctr_padre',
    ];

    /** Existen en la copia pero las escribe la aplicación sobre ella, no el origen. */
    public const COLUMNAS_DEL_DESTINO = ['id_zona'];

    /** `active`, `sv`, `sf` y `cdc` son boolean en Postgres; los tres últimos admiten nulo. */
    public const COLUMNAS_BOOLEANAS = ['active', 'sv', 'sf', 'cdc'];

    /** Primer dígito de `id_ubi` de los centros de sf. */
    private const PREFIJO_SF = '2';

    private static ?DefinicionCopia $definicionSv = null;
    private static ?DefinicionCopia $definicionSf = null;

    /** Copia de los centros de sv (`cu_centros_dl`). */
    public static function definicionSv(): DefinicionCopia
    {
        return self::$definicionSv ??= self::definicionPara(self::TABLA_SV);
    }

    /** Copia de los centros de sf (`cu_centros_dlf`). */
    public static function definicionSf(): DefinicionCopia
    {
        return self::$definicionSf ??= self::definicionPara(self::TABLA_SF);
    }

    /** La definición que corresponde a un centro, según su `id_ubi`. */
    public static function definicionDe(int|string $id_ubi): DefinicionCopia
    {
        return self::esDeSf($id_ubi) ? self::definicionSf() : self::definicionSv();
    }

    private static function definicionPara(string $tabla): DefinicionCopia
    {
        return new DefinicionCopia(
            tabla: $tabla,
            clave: 'id_ubi',
            columnas: self::COLUMNAS,
            columnasBooleanas: self::COLUMNAS_BOOLEANAS,
            columnasDelDestino: self::COLUMNAS_DEL_DESTINO,
        );
    }

    /**
     * Fila lista para escribir a partir de la entidad de centro.
     *
     * Usa el mismo converter de `f_active` que el repositorio de origen, de modo
     * que el valor es el que ya se está escribiendo en `u_centros_dl`.
     *
     * @return array<string, mixed> claves = {@see COLUMNAS}
     */
    public static function desdeCentro(object $centro): array
    {
        if (!method_exists($centro, 'toArrayForDatabase')) {
            throw new \InvalidArgumentException('La entidad no expone toArrayForDatabase()');
        }

        /** @var array<string, mixed> $aDatos */
        $aDatos = $centro->toArrayForDatabase([
            'f_active' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        return self::desdeRegistro($aDatos);
    }

    /**
     * Fila lista para escribir a partir de un registro leído de la BD.
     *
     * @param array<string, mixed> $registro
     * @return array<string, mixed>
     */
    public static function desdeRegistro(array $registro): array
    {
        return self::definicionSv()->desdeRegistro($registro);
    }

    /**
     * id_ubi de una fila, 0 si no es utilizable.
     *
     * @param array<string, mixed> $fila
     */
    public static function idUbi(array $fila): int
    {
        return self::definicionSv()->valorClave($fila);
    }

    /** ¿Es un centro de sf? Se decide por el primer dígito de `id_ubi`. */
    public static function esDeSf(int|string $id_ubi): bool
    {
        $texto = (string) $id_ubi;

        return $texto !== '' && $texto[0] === self::PREFIJO_SF;
    }

    /**
     * ¿Esta fila debe estar en la copia indicada?
     *
     * Cada copia se queda sólo con los centros de su lado; un centro con `id_ubi`
     * inutilizable no entra en ninguna.
     *
     * @param array<string, mixed> $fila
     */
    public static function debeCopiarse(array $fila, bool $paraSf): bool
    {
        $id_ubi = self::idUbi($fila);
        if ($id_ubi === 0) {
            return false;
        }

        return self::esDeSf($id_ubi) === $paraSf;
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
        return self::definicionSv()->diferencias($origen, $destino);
    }

    public static function esVerdadero(mixed $valor): bool
    {
        return ValorCopia::esVerdadero($valor);
    }
}
