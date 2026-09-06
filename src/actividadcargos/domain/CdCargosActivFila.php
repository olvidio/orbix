<?php

declare(strict_types=1);

namespace src\actividadcargos\domain;

use src\actividadcargos\domain\entity\ActividadCargo;
use src\shared\domain\copias\DefinicionCopia;
use src\shared\domain\copias\ValorCopia;

/**
 * Definición de la copia `cd_cargos_activ_dl` (BD comun): qué columnas se copian
 * y cuándo una fila debe estar en la copia.
 *
 * `cd_cargos_activ_dl` es una copia de `d_cargos_activ_dl` (BD sv-e) que vive
 * en la BD **comun**, para que las instalaciones sin acceso a sv-e (sf, DMZ)
 * puedan resolver los cargos de una actividad — en particular los sacd
 * ({@see \src\actividadcargos\infrastructure\persistence\postgresql\PgActividadCargoDlRepository::getActividadSacds}).
 *
 * Origen (BD sv-e): `<esquema>v.d_cargos_activ_dl`.
 *
 * La mecánica común a todas las copias está en {@see DefinicionCopia}; aquí, a
 * diferencia de `cp_sacd`, no hay filtro de negocio: es un espejo 1:1.
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

    private static ?DefinicionCopia $definicion = null;

    public static function definicion(): DefinicionCopia
    {
        return self::$definicion ??= new DefinicionCopia(
            tabla: 'cd_cargos_activ_dl',
            clave: 'id_item',
            columnas: self::COLUMNAS,
            columnasBooleanas: ['puede_agd'],
        );
    }

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
        return self::definicion()->desdeRegistro($registro);
    }

    /**
     * id_item de una fila, 0 si no es utilizable.
     *
     * @param array<string, mixed> $fila
     */
    public static function idItem(array $fila): int
    {
        return self::definicion()->valorClave($fila);
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
        return self::definicion()->normalizar($fila);
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
        return self::definicion()->diferencias($origen, $destino);
    }

    public static function esVerdadero(mixed $valor): bool
    {
        return ValorCopia::esVerdadero($valor);
    }
}
