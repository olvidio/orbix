<?php

declare(strict_types=1);

namespace src\ubis\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\ubis\domain\CuCentrosFila;

/**
 * Destino de una escritura en una de las dos copias de centros de la BD comun.
 *
 * Las dos copias comparten conexión (`oDBC`), esquema y estructura; sólo cambia
 * la tabla, así que un único contexto con la tabla explícita evita duplicar la
 * clase. Se construye con {@see paraSv()} / {@see paraSf()} o, en la aplicación
 * web, con {@see desdeSesionPara()}.
 */
final class CuCentrosContexto extends ContextoCopia
{
    private function __construct(
        PDO $pdoComun,
        string $esquema,
        private readonly string $tablaDestino,
        int $id_schema = 0,
    ) {
        parent::__construct($pdoComun, $esquema, '', $id_schema);
    }

    /** Copia de los centros de sv (`cu_centros_dl`). */
    public static function paraSv(PDO $pdoComun, string $esquema, int $id_schema = 0): self
    {
        return new self($pdoComun, $esquema, CuCentrosFila::TABLA_SV, $id_schema);
    }

    /** Copia de los centros de sf (`cu_centros_dlf`). */
    public static function paraSf(PDO $pdoComun, string $esquema, int $id_schema = 0): self
    {
        return new self($pdoComun, $esquema, CuCentrosFila::TABLA_SF, $id_schema);
    }

    /**
     * Contexto de la sesión web actual para el centro indicado. La conexión
     * `oDBC` ya viene con el search_path del esquema del login, así que la tabla
     * no se cualifica.
     */
    public static function desdeSesionPara(int|string $id_ubi): self
    {
        $pdoComun = GlobalPdo::get('oDBC');
        $id_schema = ConfigGlobal::mi_id_schema();

        return CuCentrosFila::esDeSf($id_ubi)
            ? new self($pdoComun, '', CuCentrosFila::TABLA_SF, $id_schema)
            : new self($pdoComun, '', CuCentrosFila::TABLA_SV, $id_schema);
    }

    public function nombreTabla(): string
    {
        return $this->tablaDestino;
    }
}
