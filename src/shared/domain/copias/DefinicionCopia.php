<?php

declare(strict_types=1);

namespace src\shared\domain\copias;

use InvalidArgumentException;

/**
 * Qué se copia en una tabla copia: nombre de destino, clave de fila y columnas.
 *
 * Una «copia» es la proyección de una o varias tablas de una base de datos
 * (interior `sv`, exterior `sv-e`, …) sobre una tabla de la BD **comun**, para
 * que las instalaciones sin acceso al origen (sf, DMZ) puedan leer ese subconjunto.
 * Casos vivos: `cp_sacd` (personas sacd) y `cd_cargos_activ_dl` (cargos de actividad).
 *
 * Esta clase concentra lo que antes estaba duplicado en cada `*Fila`: proyectar
 * un registro sobre las columnas copiadas, extraer la clave y comparar origen
 * con destino. Lo que **no** cabe aquí es el criterio de negocio de si una fila
 * concreta debe estar en la copia (`sacd IS TRUE`, la dl de los de paso, …):
 * eso vive en el módulo dueño de la copia, que es quien puede razonarlo.
 *
 * ## Columnas del destino
 *
 * `columnasDelDestino` declara columnas que **existen en la tabla copia pero no
 * vienen del origen**: las escribe la propia aplicación sobre la copia y la
 * reconciliación no debe tocarlas nunca. No se escriben, no se leen y no entran
 * en el diff.
 *
 * El caso real es `cu_centros_dlf.id_zona`: la zona SACD de un centro de sf se
 * asigna desde la instalación **sv** (`ZonaCtrUpdate`), que no puede escribir en
 * la base sf, así que ese dato sólo existe en la copia. Si la reconciliación lo
 * tratara como columna copiada, lo sobrescribiría con el `id_zona` del origen y
 * borraría todas las asignaciones de zona.
 *
 * Se declara explícitamente, en vez de limitarse a omitir la columna de
 * `columnas`, para que la decisión quede escrita, se pueda testear, y añadirla
 * por descuido a `columnas` falle en el constructor en lugar de perder datos.
 */
final class DefinicionCopia
{
    /**
     * @param string       $tabla              tabla destino en comun (p. ej. `cp_sacd`)
     * @param string       $clave              columna que identifica la fila (p. ej. `id_nom`)
     * @param list<string> $columnas           columnas copiadas desde el origen, en el orden de los INSERT
     * @param list<string> $columnasBooleanas  columnas a tratar como boolean de Postgres
     * @param bool         $claveAdmiteNegativos  las personas de paso usan `id_nom` negativo
     * @param list<string> $columnasDelDestino  columnas que existen en la tabla copia pero cuyo
     *                                          valor **no** viene del origen (ver abajo)
     */
    public function __construct(
        public readonly string $tabla,
        public readonly string $clave,
        public readonly array $columnas,
        public readonly array $columnasBooleanas = [],
        public readonly bool $claveAdmiteNegativos = false,
        public readonly array $columnasDelDestino = [],
    ) {
        if ($tabla === '' || $clave === '') {
            throw new InvalidArgumentException('Una copia necesita tabla y columna clave');
        }
        if (!in_array($clave, $columnas, true)) {
            throw new InvalidArgumentException(
                sprintf('La clave "%s" no está entre las columnas copiadas de %s', $clave, $tabla),
            );
        }
        foreach ($columnasBooleanas as $columna) {
            if (!in_array($columna, $columnas, true)) {
                throw new InvalidArgumentException(
                    sprintf('La columna booleana "%s" no está entre las columnas copiadas de %s', $columna, $tabla),
                );
            }
        }
        // Una columna del destino que también se copiase sería una contradicción:
        // la reconciliación la sobrescribiría con el valor del origen.
        foreach ($columnasDelDestino as $columna) {
            if (in_array($columna, $columnas, true)) {
                throw new InvalidArgumentException(sprintf(
                    'La columna "%s" de %s no puede ser a la vez del destino y copiada del origen',
                    $columna,
                    $tabla,
                ));
            }
        }
    }

    /**
     * Proyecta un registro cualquiera (entidad, fila de PDO) sobre las columnas
     * de la copia. Las columnas que el origen no tenga quedan a null: es el caso
     * de `id_ctr`, que existe en `p_numerarios` pero no en `p_de_paso_ex`.
     *
     * @param array<string, mixed> $registro
     * @return array<string, mixed> claves = {@see $columnas}
     */
    public function desdeRegistro(array $registro): array
    {
        $fila = [];
        foreach ($this->columnas as $columna) {
            $fila[$columna] = $registro[$columna] ?? null;
        }

        return $fila;
    }

    /**
     * Valor de la clave, o 0 si la fila no es utilizable.
     *
     * @param array<string, mixed> $fila
     */
    public function valorClave(array $fila): int
    {
        $valor = $fila[$this->clave] ?? null;
        if (!is_numeric($valor)) {
            return 0;
        }
        $clave = (int) $valor;

        return $this->claveAdmiteNegativos || $clave > 0 ? $clave : 0;
    }

    /**
     * Fila con los booleanos en la forma que acepta Postgres.
     *
     * PDO bindea los bool PHP como `'1'` / `''`, y `''` no es un boolean válido
     * en Postgres, así que hay que fijarlos antes de escribir. El `null` sí es
     * válido y se conserva: hay columnas booleanas que admiten nulo (`sv`, `sf`,
     * `cdc` en centros) y convertirlas a `'f'` cambiaría el dato del origen.
     *
     * @param array<string, mixed> $fila
     * @return array<string, mixed>
     */
    public function paraEscribir(array $fila): array
    {
        $fila = $this->desdeRegistro($fila);
        foreach ($this->columnasBooleanas as $columna) {
            if ($fila[$columna] === null) {
                continue;
            }
            $fila[$columna] = ValorCopia::esVerdadero($fila[$columna]) ? 't' : 'f';
        }

        return $fila;
    }

    /**
     * Forma canónica para comparar origen y destino sin falsos positivos.
     *
     * @param array<string, mixed> $fila
     * @return array<string, string>
     */
    public function normalizar(array $fila): array
    {
        $normalizada = [];
        foreach ($this->columnas as $columna) {
            $valor = $fila[$columna] ?? null;
            $normalizada[$columna] = in_array($columna, $this->columnasBooleanas, true)
                ? (ValorCopia::esVerdadero($valor) ? 't' : 'f')
                : ValorCopia::aTexto($valor);
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
    public function diferencias(array $origen, array $destino): array
    {
        $a = $this->normalizar($origen);
        $b = $this->normalizar($destino);

        $distintas = [];
        foreach ($this->columnas as $columna) {
            if ($a[$columna] !== $b[$columna]) {
                $distintas[] = $columna;
            }
        }

        return $distintas;
    }

    /**
     * Columnas del SET de un UPDATE: todas menos la clave, que va en el WHERE.
     *
     * @return list<string>
     */
    public function columnasActualizables(): array
    {
        return array_values(array_filter(
            $this->columnas,
            fn(string $columna): bool => $columna !== $this->clave,
        ));
    }
}
