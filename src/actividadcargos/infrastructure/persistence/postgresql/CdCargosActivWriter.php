<?php

declare(strict_types=1);

namespace src\actividadcargos\infrastructure\persistence\postgresql;

use src\actividadcargos\domain\CdCargosActivFila;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\shared\infrastructure\persistence\copias\CopiaWriter;

/**
 * Escrituras y lecturas sobre `cd_cargos_activ_dl`.
 *
 * Toda la mecánica (upsert sin `ON CONFLICT`, borrado por lotes, lectura para el
 * diff) está en {@see CopiaWriter}; aquí sólo se ata la definición de la copia.
 */
class CdCargosActivWriter extends CopiaWriter
{
    public function __construct()
    {
        parent::__construct(CdCargosActivFila::definicion());
    }

    /**
     * id_item de filas cuyo `id_schema` no es el del contexto.
     *
     * @return list<int>
     */
    public function idItemsDeOtroEsquema(ContextoCopia $contexto): array
    {
        return $this->clavesDeOtroEsquema($contexto);
    }
}
