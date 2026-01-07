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

    private MapIdResto $id_resto;

    private MapIdDl $id_dl;

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
        return $this->id_resto->value();
    }

    /**
     * @deprecated
     */
    public function setId_resto(int $id_resto): void
    {
        $this->id_resto = new MapIdResto($id_resto);
    }

    // Value Object API for id_resto
    public function getIdRestoVo(): MapIdResto
    {
        return $this->id_resto;
    }

    public function setIdRestoVo(MapIdResto|int $id): void
    {
        $this->id_resto = $id instanceof MapIdResto
            ? $id
            : new MapIdResto($id);
    }

    /**
     * @deprecated
     */
    public function getId_dl(): int
    {
        return $this->id_dl->value();
    }

    /**
     * @deprecated
     */
    public function setId_dl(int $id_dl): void
    {
        $this->id_dl = new MapIdDl($id_dl);
    }

    // Value Object API for id_dl
    public function getIdDlVo(): MapIdDl
    {
        return $this->id_dl;
    }

    public function setIdDlVo(MapIdDl|int $id): void
    {
        $this->id_dl = $id instanceof MapIdDl
            ? $id
            : new MapIdDl($id);
    }
}