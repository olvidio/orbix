<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\EncargoDescText;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\encargossacd\domain\value_objects\EncargoOrden;
use src\encargossacd\domain\value_objects\EncargoPrioridad;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\LugarDescText;
use src\encargossacd\domain\value_objects\ObservText;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\LocaleCode;
use src\shared\domain\value_objects\SfsvId;


class Encargo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_enc;

    private EncargoTipoId $id_tipo_enc;

    private EncargoGrupo $grupo_encargo;

    private ?int $id_ubi = null;

    private ?int $id_zona = null;

    private EncargoDescText|null $desc_enc = null;

    private LocaleCode|null $idioma_enc = null;

    private LugarDescText|null $desc_lugar = null;

    private ObservText|null $observ = null;

    private EncargoOrden|null $orden = null;

    private EncargoPrioridad|null $prioridad = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_enc(): int
    {
        return $this->id_enc;
    }


    public function setId_enc(int $id_enc): void
    {
        $this->id_enc = $id_enc;
    }

    /**
     * @deprecated Usar `getTipoEncVo(): EncargoTipoId` en su lugar.
     */
    public function getId_tipo_enc(): int
    {
        return $this->id_tipo_enc->value();
    }

    /**
     * @deprecated Usar `setTipoEncVo(EncargoTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_enc(int $id_tipo_enc): void
    {
        $this->id_tipo_enc = new EncargoTipoId($id_tipo_enc);
    }

    public function getTipoEncVo(): EncargoTipoId
    {
        return $this->id_tipo_enc;
    }

    public function setTipoEncVo(EncargoTipoId|int $vo): void
    {
        $this->id_tipo_enc = $vo instanceof EncargoTipoId
            ? $vo
            : EncargoTipoId::fromNullableInt($vo);
    }


    /**
     * @deprecated Usar `getEncargoGrupoVo(): EncargoGrupo` en su lugar.
     */
    public function getGrupo_encargo(): int
    {
        return $this->grupo_encargo->value();
    }

    /**
     * @deprecated Usar `setEncargoGrupoVo(EncargoGrupo $vo): void` en su lugar.
     */
    public function setGrupo_encargo(int $isf_sv): void
    {
        $this->grupo_encargo = new EncargoGrupo($isf_sv);
    }

    public function getGrupoEncargoVo(): EncargoGrupo
    {
        return $this->grupo_encargo;
    }

    public function setGrupoEncargoVo(EncargoGrupo|int $vo): void
    {
        $this->grupo_encargo = $vo instanceof EncargoGrupo
            ? $vo
            : EncargoGrupo::fromNullableInt($vo);
    }


    public function getId_ubi(): ?int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(?int $id_ubi = null): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getId_zona(): ?int
    {
        return $this->id_zona;
    }


    public function setId_zona(?int $id_zona = null): void
    {
        $this->id_zona = $id_zona;
    }

    /**
     * @deprecated Usar `getDescEncVo(): ?EncargoDescText` en su lugar.
     */
    public function getDesc_enc(): ?string
    {
        return $this->desc_enc?->value();
    }

    /**
     * @deprecated Usar `setDescEncVo(?EncargoDescText $vo): void` en su lugar.
     */
    public function setDesc_enc(?string $desc_enc = null): void
    {
        $this->desc_enc = EncargoDescText::fromNullableString($desc_enc);
    }

    public function getDescEncVo(): ?EncargoDescText
    {
        return $this->desc_enc;
    }

    public function setDescEncVo(EncargoDescText|string|null $texto): void
    {
        $this->desc_enc = $texto instanceof EncargoDescText
            ? $texto
            : EncargoDescText::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getIdiomaEncVo(): ?LocaleCode` en su lugar.
     */
    public function getIdioma_enc(): ?string
    {
        return $this->idioma_enc?->value();
    }

    /**
     * @deprecated Usar `setIdiomaEncVo(?LocaleCode $vo): void` en su lugar.
     */
    public function setIdioma_enc(?string $idioma_enc = null): void
    {
        $this->idioma_enc = LocaleCode::fromNullableString($idioma_enc);
    }

    public function getIdiomaEncVo(): ?LocaleCode
    {
        return $this->idioma_enc;
    }

    public function setIdiomaEncVo(LocaleCode|string|null $texto): void
    {
        $this->idioma_enc = $texto instanceof LocaleCode
            ? $texto
            : LocaleCode::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getDescLugarVo(): ?LugarDescText` en su lugar.
     */
    public function getDesc_lugar(): ?string
    {
        return $this->desc_lugar?->value();
    }

    /**
     * @deprecated Usar `setDescLugarVo(?LugarDescText $vo): void` en su lugar.
     */
    public function setDesc_lugar(?string $desc_lugar = null): void
    {
        $this->desc_lugar = LugarDescText::fromNullableString($desc_lugar);
    }

    public function getDescLugarVo(): ?LugarDescText
    {
        return $this->desc_lugar;
    }

    public function setDescLugarVo(LugarDescText|string|null $texto): void
    {
        $this->desc_lugar = $texto instanceof LugarDescText
            ? $texto
            : LugarDescText::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getObservVo(): ?ObservText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated Usar `setObservVo(?ObservText $vo): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservText
    {
        return $this->observ;
    }

    public function setObservVo(ObservText|string|null $texto): void
    {
        $this->observ = $texto instanceof ObservText
            ? $texto
            : ObservText::fromNullableString($texto);
    }

    /**
     * @deprecated Usar `getOrdenVo(): ?EncargoOrden` en su lugar.
     */
    public function getOrden(): ?int
    {
        return $this->orden?->value();
    }

    /**
     * @deprecated Usar `setOrdenVo(?EncargoOrden $vo): void` en su lugar.
     */
    public function setOrden(?int $orden = null): void
    {
        $this->orden = EncargoOrden::fromNullableInt($orden);
    }

    public function getOrdenVo(): ?EncargoOrden
    {
        return $this->orden;
    }

    public function setOrdenVo(EncargoOrden|int|null $valor): void
    {
        $this->orden = $valor instanceof EncargoOrden
            ? $valor
            : EncargoOrden::fromNullableInt($valor);
    }


    /**
     * @deprecated Usar `getPrioridadVo(): ?EncargoPrioridad` en su lugar.
     */
    public function getPrioridad(): ?int
    {
        return $this->prioridad?->value();
    }

    /**
     * @deprecated Usar `setPrioridadVo(?EncargoPrioridad $vo): void` en su lugar.
     */
    public function setPrioridad(?int $prioridad = null): void
    {
        $this->prioridad = EncargoPrioridad::fromNullableInt($prioridad);
    }

    public function getPrioridadVo(): ?EncargoPrioridad
    {
        return $this->prioridad;
    }

    public function setPrioridadVo(EncargoPrioridad|int|null $valor): void
    {
        $this->prioridad = $valor instanceof EncargoPrioridad
            ? $valor
            : EncargoPrioridad::fromNullableInt($valor);
    }
}