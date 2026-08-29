<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;

/**
 * Destino de una escritura en `cp_sacd`: conexión a la BD comun, esquema y dl.
 *
 * Existen dos formas de obtenerlo y por eso el contexto es explícito:
 *   - {@see desdeSesion()} en la aplicación web (conexión y esquema del login).
 *   - construido a mano por {@see \src\personas\application\ResincronizarCpSacd},
 *     que recorre todos los esquemas con una conexión de mantenimiento y por
 *     tanto no tiene sesión de la que tirar.
 */
final class CpSacdContexto
{
    /**
     * @param PDO    $pdoComun   conexión a la BD comun (escritura, no réplica)
     * @param string $esquema    esquema comun (p. ej. `H-dlb`); vacío = usar el search_path
     * @param string $dl         delegación del esquema (p. ej. `dlb`)
     * @param int    $id_schema  id del esquema en `public.db_idschema`; 0 = dejar el DEFAULT
     */
    public function __construct(
        public readonly PDO $pdoComun,
        public readonly string $esquema,
        public readonly string $dl,
        public readonly int $id_schema = 0,
    ) {
    }

    /**
     * Contexto de la sesión web actual. La conexión `oDBC` ya viene con el
     * search_path del esquema del login, así que la tabla no se cualifica.
     */
    public static function desdeSesion(): self
    {
        return new self(
            GlobalPdo::get('oDBC'),
            '',
            // delef (con sufijo `f` en sf): es la forma que llevan las filas de personas,
            // como se ve en la comprobación de PersonaEliminar.
            ConfigGlobal::mi_delef(),
            ConfigGlobal::mi_id_schema(),
        );
    }

    /** Nombre de tabla a usar en el SQL, cualificado sólo si hace falta. */
    public function tabla(): string
    {
        if ($this->esquema === '') {
            return 'cp_sacd';
        }

        return '"' . str_replace('"', '""', $this->esquema) . '".cp_sacd';
    }

    /** Delegación a partir del nombre de esquema, con la misma regla que ConfigGlobal::mi_dele(). */
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
}
