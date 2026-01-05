<?php

namespace src\actividades\domain\entity;
use src\shared\domain\traits\Hydratable;


class Importada
{
    use Hydratable;

    private int $id_activ;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }
}