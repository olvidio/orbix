<?php

namespace src\encargossacd\domain\entity;

/**
 * Fila de la tabla staging `propuesta_encargos_sacd` (copia editable de encargos_sacd).
 */
class PropuestaEncargoSacd extends EncargoSacd
{
    private ?int $id_nom_new = null;

    public function getId_nom_new(): ?int
    {
        return $this->id_nom_new;
    }

    public function setId_nom_new(?int $id_nom_new): void
    {
        $this->id_nom_new = $id_nom_new;
    }
}
