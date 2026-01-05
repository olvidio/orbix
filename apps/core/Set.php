<?php

namespace core;

class Set
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * getTot() Array de objetos
     *
     * @var array
     */
    private array $aCollection = [];
    private int $count = 0;

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    public function add($oElement): void
    {
        $this->aCollection[$this->count++] = $oElement;
    }

    public function getTot(): array
    {
        return $this->aCollection;
    }

    public function getElement($count)
    {
        return $this->aCollection[$count];
    }

    public function setElement($count, $oElement): void
    {
        $this->aCollection[$count] = $oElement;
    }

}