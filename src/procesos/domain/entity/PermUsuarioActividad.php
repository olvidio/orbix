<?php

namespace src\procesos\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\procesos\domain\value_objects\FaseId;
use src\shared\domain\traits\Hydratable;
use function core\is_true;


class PermUsuarioActividad
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private ?int $id_usuario = null;

    private bool $dl_propia;

    private ?ActividadTipoId $id_tipo_activ_txt = null;

    private ?FaseId $fase_ref = null;

    private ?int $afecta_a = null;

    private ?int $perm_on = null;

    private ?int $perm_off = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_usuario(): ?int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(?int $id_usuario = null): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function isDl_propia(): bool
    {
        return $this->dl_propia;
    }


    public function setDl_propia(bool $dl_propia): void
    {
        $this->dl_propia = $dl_propia;
    }


    public function getId_tipo_activ_txt(): ?string
    {
        return $this->id_tipo_activ_txt?->value();
    }
    public function getIdTipoActivTxtVo(): ActividadTipoId
    {
        return $this->id_tipo_activ_txt;
    }

    public function setId_tipo_activ_txt(?string $id_tipo_activ_txt = null): void
    {
        $this->id_tipo_activ_txt = $id_tipo_activ_txt;
    }
    public function setIdTipoActivTxtVo(ActividadTipoId|string|int|null $id_tipo_activ_txt): void
    {
        $this->id_tipo_activ_txt = $id_tipo_activ_txt instanceof ActividadTipoId
            ? $id_tipo_activ_txt
            : ActividadTipoId::fromInt($id_tipo_activ_txt);
    }


    public function getFaseRefVo(): ?FaseId
    {
        return $this->fase_ref;
    }

    public function setFaseRefVo(FaseId|int|null $fase_ref = null): void
    {
        $this->fase_ref = $fase_ref instanceof FaseId
            ? $fase_ref
            : FaseId::fromNullableInt($fase_ref);
    }

    /**
     * @deprecated use getFaseRefVo()
     */
    public function getFase_ref(): ?int
    {
        return $this->fase_ref?->value();
    }

    /**
     * @deprecated use setFaseRefVo()
     */
    public function setFase_ref(?int $fase_ref = null): void
    {
        $this->fase_ref = FaseId::fromNullableInt($fase_ref);
    }


    public function getAfecta_a(): ?int
    {
        return $this->afecta_a;
    }


    public function setAfecta_a(?int $afecta_a = null): void
    {
        $this->afecta_a = $afecta_a;
    }


    public function getPerm_on(): ?int
    {
        return $this->perm_on;
    }


    public function setPerm_on(?int $perm_on = null): void
    {
        $this->perm_on = $perm_on;
    }


    public function getPerm_off(): ?int
    {
        return $this->perm_off;
    }


    public function setPerm_off(?int $perm_off = null): void
    {
        $this->perm_off = $perm_off;
    }
}