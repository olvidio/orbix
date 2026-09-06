<?php

declare(strict_types=1);

namespace src\ubis\application;

use src\ubis\infrastructure\persistence\postgresql\CuCentrosDlWriter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Reconcilia la copia `cu_centros_dl` (BD comun) con `u_centros_dl` de la BD
 * **sv**, en todos los esquemas o en uno concreto.
 *
 * Sólo tiene sentido en la instalación **sv**: es la que tiene el origen de los
 * centros de sv. El driver CLI lo comprueba.
 */
final class ResincronizarCuCentrosDl extends ResincronizarCuCentros
{
    public function __construct(DbSchemaRepositoryInterface $dbSchemaRepository, CuCentrosDlWriter $writer)
    {
        parent::__construct($dbSchemaRepository, $writer);
    }

    protected function esCopiaDeSf(): bool
    {
        return false;
    }
}
