<?php

namespace src\shared\infrastructure\persistence\postgresql;

class Set
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * getTot() Array de objetos
     *
     * @var array<int, mixed>
     */
    private array $aCollection = [];
    private int $count = 0;

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    public function add(mixed $oElement): void
    {
        $this->aCollection[$this->count++] = $oElement;
    }

    /**
     * @return array<int, mixed>
     */
    public function getTot(): array
    {
        return $this->aCollection;
    }

    public function getElement(int $count): mixed
    {
        return $this->aCollection[$count];
    }

    public function setElement(int $count, mixed $oElement): void
    {
        $this->aCollection[$count] = $oElement;
    }

}
