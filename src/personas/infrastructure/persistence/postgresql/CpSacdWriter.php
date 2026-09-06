<?php

declare(strict_types=1);

namespace src\personas\infrastructure\persistence\postgresql;

use src\personas\domain\CpSacdFila;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\shared\infrastructure\persistence\copias\CopiaWriter;

/**
 * Escrituras y lecturas sobre `cp_sacd`.
 *
 * Toda la mecánica (upsert sin `ON CONFLICT`, borrado por lotes, lectura para el
 * diff) está en {@see CopiaWriter}; aquí sólo se ata la definición de la copia.
 */
class CpSacdWriter extends CopiaWriter
{
    public function __construct()
    {
        parent::__construct(CpSacdFila::definicion());
    }

    /**
     * id_nom de filas cuyo `id_schema` no es el del contexto.
     *
     * @return list<int>
     */
    public function idNomsDeOtroEsquema(ContextoCopia $contexto): array
    {
        return $this->clavesDeOtroEsquema($contexto);
    }
}
