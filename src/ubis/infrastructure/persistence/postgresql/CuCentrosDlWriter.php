<?php

declare(strict_types=1);

namespace src\ubis\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\copias\CopiaWriter;
use src\ubis\domain\CuCentrosFila;

/**
 * Escrituras y lecturas sobre `cu_centros_dl` (copia de los centros de sv).
 *
 * Toda la mecánica está en {@see CopiaWriter}; aquí sólo se ata la definición.
 */
class CuCentrosDlWriter extends CopiaWriter
{
    public function __construct()
    {
        parent::__construct(CuCentrosFila::definicionSv());
    }
}
