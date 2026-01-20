<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\shared\domain\value_objects\SfsvOtrosId;

class CasaPeriodo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ubi;

    private DateTimeLocal $f_ini;

    private DateTimeLocal $f_fin;

    private ?SfsvOtrosId $sfsv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }


    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }


    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin ?? new NullDateTimeLocal;
    }


    public function setF_fin(DateTimeLocal|null $f_fin = null): void
    {
        $this->f_fin = $f_fin;
    }

    /**
     * @deprecated usar getSfsvVo()
     */
    public function getSfsv(): ?int
    {
        return $this->sfsv->value();
    }

    /**
     * @deprecated usar setSfsvVo()
     */
    public function setSfsv(?int $sfsv = null): void
    {
        $this->sfsv = SfsvOtrosId::fromNullableInt($sfsv);
    }

    public function getSfsvVo(): ?SfsvOtrosId
    {
        return $this->sfsv;
    }

    public function setSfsvVo(SfsvOtrosId|null|int $vo = null): void
    {
        $this->sfsv = $vo instanceof SfsvOtrosId
            ? $vo
            : SfsvOtrosId::fromNullableInt($vo);
    }
}