<?php

declare(strict_types=1);

namespace src\ubis\application;

use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlfWriter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Reconcilia la copia `cu_centros_dlf` (BD comun) con `u_centros_dl` de la BD
 * **sf**, en todos los esquemas o en uno concreto.
 *
 * Se ejecuta en la instalación **sf**, que es la dueña de esos centros. Desde sv
 * habría que llegar al origen por `importar`/`publicf` (como las migraciones de
 * la serie sf), pero no se hace: sv no escribe en sf, y la copia se mantiene con
 * la sincronización incremental de la propia sf. El driver CLI comprueba la
 * ubicación.
 */
final class ResincronizarCuCentrosDlf extends ResincronizarCuCentros
{
    public function __construct(DbSchemaRepositoryInterface $dbSchemaRepository, CuCentrosDlfWriter $writer)
    {
        parent::__construct($dbSchemaRepository, $writer);
    }

    protected function esCopiaDeSf(): bool
    {
        return true;
    }
}
