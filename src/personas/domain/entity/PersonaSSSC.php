<?php

namespace src\personas\domain\entity;


class PersonaSSSC extends PersonaDl
{

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_auto;


    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_auto(): int
    {
        return $this->id_auto;
    }


    public function setId_auto(int $id_auto): void
    {
        $this->id_auto = $id_auto;
    }


}
