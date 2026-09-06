<?php

declare(strict_types=1);

namespace src\shared\domain\copias;

/**
 * Conversiones de valor compartidas por todas las copias entre bases de datos.
 *
 * Origen y destino viven en conexiones distintas y no siempre devuelven el mismo
 * tipo PHP para el mismo dato (`true` frente a `'t'`, `3` frente a `'3'`,
 * `null` frente a `''`). Sin una forma canónica, el diff de la reconciliación
 * marcaría como distintas filas que en realidad son iguales y reescribiría la
 * copia entera en cada pasada.
 */
final class ValorCopia
{
    /**
     * Representación textual comparable de un valor de columna.
     *
     * `null` y cadena vacía colapsan al mismo texto a propósito: en Postgres una
     * columna de texto vacía y una nula son indistinguibles para lo que la copia
     * necesita, y distinguirlas produciría cambios fantasma.
     */
    public static function aTexto(mixed $valor): string
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

    /**
     * Interpreta como booleano lo que puede llegar de PDO, de una entidad o de
     * un formulario.
     */
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
