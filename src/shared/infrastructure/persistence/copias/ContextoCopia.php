<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\copias;

use PDO;

/**
 * Destino de una escritura en una tabla copia: conexión a comun, esquema y dl.
 *
 * El destino es explícito (y no se deduce de la sesión dentro del writer) porque
 * hay dos formas de llegar a él:
 *   - la aplicación web, con la conexión y el esquema del login;
 *   - la reconciliación por cron, que recorre esquemas ajenos con una conexión
 *     de mantenimiento y por tanto no tiene sesión de la que tirar.
 *
 * Cada copia concreta hereda y fija el nombre de su tabla.
 */
abstract class ContextoCopia
{
    /**
     * @param PDO    $pdoComun   conexión a la BD comun (escritura, no réplica)
     * @param string $esquema    esquema comun (p. ej. `H-dlb`); vacío = usar el search_path
     * @param string $dl         delegación del esquema (p. ej. `dlb`); vacío si la copia no la necesita
     * @param int    $id_schema  id del esquema en `public.db_idschema`; 0 = dejar el DEFAULT
     */
    public function __construct(
        public readonly PDO $pdoComun,
        public readonly string $esquema,
        public readonly string $dl = '',
        public readonly int $id_schema = 0,
    ) {
    }

    /** Nombre de la tabla copia, sin cualificar. */
    abstract public function nombreTabla(): string;

    /** Nombre de tabla a usar en el SQL, cualificado sólo si hace falta. */
    public function tabla(): string
    {
        if ($this->esquema === '') {
            return $this->nombreTabla();
        }

        return self::comillas($this->esquema) . '.' . $this->nombreTabla();
    }

    /**
     * Delegación a partir del nombre de esquema, con la misma regla que
     * `ConfigGlobal::mi_dele()`: `H-dlbv` → `dlb`, y las regiones `cr` conservan
     * el prefijo para no colisionar entre sí.
     */
    public static function dlDeEsquema(string $esquema): string
    {
        $partes = explode('-', $esquema, 2);
        if (count($partes) < 2 || $partes[1] === '') {
            return '';
        }

        $dl = $partes[1];
        $ultimo = substr($dl, -1);
        if ($ultimo === 'v' || $ultimo === 'f') {
            $dl = substr($dl, 0, -1);
        }
        if ($dl === 'cr') {
            $dl .= $partes[0];
        }

        return $dl;
    }

    public static function comillas(string $identificador): string
    {
        return '"' . str_replace('"', '""', $identificador) . '"';
    }
}
