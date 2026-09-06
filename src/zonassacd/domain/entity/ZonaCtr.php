<?php

declare(strict_types=1);

namespace src\zonassacd\domain\entity;

use src\shared\domain\traits\Hydratable;

/**
 * Relación centro ↔ zona SACD. Un centro pertenece como mucho a una zona;
 * la ausencia de fila significa que no tiene ninguna asignada.
 */
class ZonaCtr
{
    use Hydratable;

    private int $id_ubi;

    private int $id_zona;

    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }

    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }

    public function getId_zona(): int
    {
        return $this->id_zona;
    }

    public function setId_zona(int $id_zona): void
    {
        $this->id_zona = $id_zona;
    }
}
