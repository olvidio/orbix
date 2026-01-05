<?php

namespace src\utils_database\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use src\utils_database\domain\value_objects\MapObjectCode;


class MapId
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private MapObjectCode $objeto;

    private int $id_resto;

    private int $id_dl;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated
     */
    public function getObjeto(): string
    {
        return $this->objeto->value();
    }

    /**
     * @deprecated
     */
    public function setObjeto(string $objeto): void
    {
        $this->objeto = new MapObjectCode($objeto);
    }

    // Value Object API for objeto
    public function getObjetoVo(): MapObjectCode
    {
        return $this->objeto;
    }

    public function setObjetoVo(MapObjectCode|string $code): void
    {
        $this->objeto = $code instanceof MapObjectCode ? $code : new MapObjectCode($code);
    }

    /**
     * @deprecated
     */
    public function getId_resto(): int
    {
        return $this->id_resto;
    }

    /**
     * @deprecated
     */
    public function setId_resto(int $id_resto): void
    {
        $this->id_resto = $id_resto;
    }

    // Value Object API for id_resto
    public function getIdRestoVo(): MapIdResto
    {
        return new MapIdResto($this->id_resto);
    }

    public function setIdRestoVo(MapIdResto $id): void
    {
        $this->id_resto = $id->value();
    }

    /**
     * @deprecated
     */
    public function getId_dl(): int
    {
        return $this->id_dl;
    }

    /**
     * @deprecated
     */
    public function setId_dl(int $id_dl): void
    {
        $this->id_dl = $id_dl;
    }

    // Value Object API for id_dl
    public function getIdDlVo(): MapIdDl
    {
        return new MapIdDl($this->id_dl);
    }

    public function setIdDlVo(MapIdDl $id): void
    {
        $this->id_dl = $id->value();
    }
}