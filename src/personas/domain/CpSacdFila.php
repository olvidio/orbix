<?php

declare(strict_types=1);

namespace src\personas\domain;

use src\shared\domain\copias\DefinicionCopia;
use src\shared\domain\copias\ValorCopia;
use src\shared\infrastructure\persistence\ConverterDate;

/**
 * Definición de la copia `cp_sacd` (BD comun): qué columnas se copian, desde qué
 * orígenes, y cuándo una fila debe estar en la copia.
 *
 * `cp_sacd` es una copia de las personas marcadas como SACD que vive en la BD
 * **comun**, para que las instalaciones sin acceso a la BD interior (sf, DMZ)
 * puedan resolver nombres de sacerdotes. Ver {@see \src\cambios\application\PersonaNombreParaAviso}.
 *
 * Orígenes (BD interior):
 *   - `<esquema>v.p_numerarios`  → id_tabla `n`
 *   - `<esquema>v.p_agregados`   → id_tabla `a`
 *   - `<esquema>v.p_sssc`        → id_tabla `sssc`
 *   - `restov.p_de_paso_ex`      → id_tabla `pn` / `pa`, sólo los de la dl propia
 *
 * Quedan fuera `PersonaS` y `PersonaNax` (criterio histórico: el legacy
 * `PersonaS::DBGuardar()` tampoco llamaba a `copia2Comun()`).
 *
 * La mecánica común a todas las copias (proyectar, comparar, normalizar) está en
 * {@see DefinicionCopia}; aquí queda lo propio de los sacd, que es el criterio
 * de {@see debeCopiarse()}.
 */
final class CpSacdFila
{
    /** Columnas que se copian a `cp_sacd`, en el orden de los INSERT. */
    public const COLUMNAS = [
        'id_nom',
        'id_tabla',
        'dl',
        'sacd',
        'trato',
        'nom',
        'nx1',
        'apellido1',
        'nx2',
        'apellido2',
        'f_nacimiento',
        'idioma_preferido',
        'situacion',
        'f_situacion',
        'apel_fam',
        'inc',
        'f_inc',
        'nivel_stgr',
        'profesion',
        'eap',
        'observ',
        'id_ctr',
        'lugar_nacimiento',
    ];

    /** id_tabla que alimentan la copia. */
    public const ID_TABLAS = ['n', 'a', 'pn', 'pa', 'sssc'];

    /** id_tabla de personas de paso: sólo se copian si están en la dl propia. */
    public const ID_TABLAS_DE_PASO = ['pn', 'pa'];

    private static ?DefinicionCopia $definicion = null;

    /**
     * Los `id_nom` de las personas de paso son negativos, así que la clave
     * admite negativos: sólo el 0 (o lo no numérico) invalida la fila.
     */
    public static function definicion(): DefinicionCopia
    {
        return self::$definicion ??= new DefinicionCopia(
            tabla: 'cp_sacd',
            clave: 'id_nom',
            columnas: self::COLUMNAS,
            columnasBooleanas: ['sacd'],
            claveAdmiteNegativos: true,
        );
    }

    /**
     * Fila lista para escribir en `cp_sacd` a partir de una entidad de persona.
     *
     * Usa el mismo `toArrayForDatabase()` con los mismos converters que emplea el
     * repositorio de la tabla origen, de modo que los valores son exactamente los
     * que ya se están escribiendo en la BD interior.
     *
     * @return array<string, mixed> claves = {@see COLUMNAS}
     */
    public static function desdePersona(object $persona): array
    {
        if (!method_exists($persona, 'toArrayForDatabase')) {
            throw new \InvalidArgumentException('La entidad no expone toArrayForDatabase()');
        }

        /** @var array<string, mixed> $aDatos */
        $aDatos = $persona->toArrayForDatabase([
            'f_nacimiento' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_situacion' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_inc' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        return self::desdeRegistro($aDatos);
    }

    /**
     * Fila lista para escribir a partir de un registro leído de la BD.
     * Las columnas ausentes (p. ej. `id_ctr` en `p_de_paso_ex`) quedan a null.
     *
     * @param array<string, mixed> $registro
     * @return array<string, mixed>
     */
    public static function desdeRegistro(array $registro): array
    {
        return self::definicion()->desdeRegistro($registro);
    }

    /**
     * id_nom de una fila, 0 si no es utilizable (negativos son válidos: personas de paso).
     *
     * @param array<string, mixed> $fila
     */
    public static function idNom(array $fila): int
    {
        return self::definicion()->valorClave($fila);
    }

    /**
     * ¿Esta persona debe estar en `cp_sacd` de la dl indicada?
     *
     * @param array<string, mixed> $fila
     */
    public static function debeCopiarse(array $fila, string $dlPropia): bool
    {
        if (!self::esVerdadero($fila['sacd'] ?? null)) {
            return false;
        }

        $id_tabla = is_scalar($fila['id_tabla'] ?? null) ? (string) $fila['id_tabla'] : '';
        if (!in_array($id_tabla, self::ID_TABLAS, true)) {
            return false;
        }

        // Los de paso sólo mientras están en la dl: cuando se van, se borran de la copia.
        if (in_array($id_tabla, self::ID_TABLAS_DE_PASO, true)) {
            $dl = is_scalar($fila['dl'] ?? null) ? (string) $fila['dl'] : '';

            return $dlPropia !== '' && $dl === $dlPropia;
        }

        return true;
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
