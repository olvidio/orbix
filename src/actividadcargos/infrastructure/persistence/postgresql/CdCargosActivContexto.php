<?php

declare(strict_types=1);

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;

/**
 * Destino de una escritura en `cd_cargos_activ_dl`: conexión a la BD comun y esquema.
 *
 * Existen dos formas de obtenerlo y por eso el contexto es explícito:
 *   - {@see desdeSesion()} en la aplicación web (conexión y esquema del login).
 *   - construido a mano por {@see \src\actividadcargos\application\ResincronizarCdCargosActiv},
 *     que recorre todos los esquemas con una conexión de mantenimiento y por
 *     tanto no tiene sesión de la que tirar.
 */
final class CdCargosActivContexto
{
    /**
     * @param PDO    $pdoComun   conexión a la BD comun (escritura, no réplica)
     * @param string $esquema    esquema comun (p. ej. `H-dlb`); vacío = usar el search_path
     * @param int    $id_schema  id del esquema en `public.db_idschema`; 0 = dejar el DEFAULT
     */
    public function __construct(
        public readonly PDO $pdoComun,
        public readonly string $esquema,
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
            ConfigGlobal::mi_id_schema(),
        );
    }

    /** Nombre de tabla a usar en el SQL, cualificado sólo si hace falta. */
    public function tabla(): string
    {
        if ($this->esquema === '') {
            return 'cd_cargos_activ_dl';
        }

        return '"' . str_replace('"', '""', $this->esquema) . '".cd_cargos_activ_dl';
    }
}
