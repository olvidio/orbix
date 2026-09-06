<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\copias\ContextoCopia;

/**
 * Destino de una escritura en `cp_sacd`: conexión a la BD comun, esquema y dl.
 *
 * Existen dos formas de obtenerlo y por eso el contexto es explícito:
 *   - {@see desdeSesion()} en la aplicación web (conexión y esquema del login).
 *   - construido a mano por {@see \src\personas\application\ResincronizarCpSacd},
 *     que recorre todos los esquemas con una conexión de mantenimiento y por
 *     tanto no tiene sesión de la que tirar.
 */
final class CpSacdContexto extends ContextoCopia
{
    /**
     * @param PDO    $pdoComun   conexión a la BD comun (escritura, no réplica)
     * @param string $esquema    esquema comun (p. ej. `H-dlb`); vacío = usar el search_path
     * @param string $dl         delegación del esquema (p. ej. `dlb`)
     * @param int    $id_schema  id del esquema en `public.db_idschema`; 0 = dejar el DEFAULT
     */
    public function __construct(PDO $pdoComun, string $esquema, string $dl, int $id_schema = 0)
    {
        parent::__construct($pdoComun, $esquema, $dl, $id_schema);
    }

    public function nombreTabla(): string
    {
        return 'cp_sacd';
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
}
