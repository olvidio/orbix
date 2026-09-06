<?php

declare(strict_types=1);

namespace src\ubis\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\copias\CopiaWriter;
use src\ubis\domain\CuCentrosFila;

/**
 * Escrituras y lecturas sobre `cu_centros_dlf` (copia de los centros de sf).
 *
 * Toda la mecánica está en {@see CopiaWriter}; aquí sólo se ata la definición.
 * `id_zona` no se escribe nunca: es columna del destino
 * ({@see CuCentrosFila::COLUMNAS_DEL_DESTINO}).
 */
class CuCentrosDlfWriter extends CopiaWriter
{
    public function __construct()
    {
        parent::__construct(CuCentrosFila::definicionSf());
    }
}
