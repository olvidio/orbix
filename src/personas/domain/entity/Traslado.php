<?php

namespace src\personas\domain\entity;

use src\personas\domain\value_objects\NombreCentroText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\TrasladoTipoCmbCode;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;


class Traslado
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private DateTimeLocal|null $f_traslado = null;

    private string $tipo_cmb;

    private int|null $id_ctr_origen = null;

    private string|null $ctr_origen = null;

    private int|null $id_ctr_destino = null;

    private string $ctr_destino;

    private string|null $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function getF_traslado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_traslado ?? new NullDateTimeLocal;
    }


    public function setF_traslado(DateTimeLocal|null $f_traslado = null): void
    {
        $this->f_traslado = $f_traslado;
    }

    /**
     * @deprecated use getTipoCmbVo()
     */
    public function getTipo_cmb(): string
    {
        return $this->tipo_cmb;
    }

    /**
     * @deprecated use setTipoCmbVo()
     */
    public function setTipo_cmb(string $tipo_cmb): void
    {
        $this->tipo_cmb = $tipo_cmb;
    }

    public function getTipoCmbVo(): TrasladoTipoCmbCode
    {
        return new TrasladoTipoCmbCode($this->tipo_cmb);
    }

    public function setTipoCmbVo(TrasladoTipoCmbCode $vo): void
    {
        $this->tipo_cmb = $vo->value();
    }


    public function getId_ctr_origen(): ?int
    {
        return $this->id_ctr_origen;
    }


    public function setId_ctr_origen(?int $id_ctr_origen = null): void
    {
        $this->id_ctr_origen = $id_ctr_origen;
    }

    /**
     * @deprecated use getCtrOrigenVo()
     */
    public function getCtr_origen(): ?string
    {
        return $this->ctr_origen;
    }

    /**
     * @deprecated use setCtrOrigenVo()
     */
    public function setCtr_origen(?string $ctr_origen = null): void
    {
        $this->ctr_origen = $ctr_origen;
    }

    public function getCtrOrigenVo(): ?NombreCentroText
    {
        return NombreCentroText::fromNullableString($this->ctr_origen);
    }

    public function setCtrOrigenVo(?NombreCentroText $vo = null): void
    {
        $this->ctr_origen = $vo?->value();
    }


    public function getId_ctr_destino(): ?int
    {
        return $this->id_ctr_destino;
    }


    public function setId_ctr_destino(?int $id_ctr_destino = null): void
    {
        $this->id_ctr_destino = $id_ctr_destino;
    }

    /**
     * @deprecated use getCtrDestinoVo()
     */
    public function getCtr_destino(): string
    {
        return $this->ctr_destino;
    }

    /**
     * @deprecated use setCtrDestinoVo()
     */
    public function setCtr_destino(string $ctr_destino): void
    {
        $this->ctr_destino = $ctr_destino;
    }

    public function getCtrDestinoVo(): NombreCentroText
    {
        return new NombreCentroText($this->ctr_destino);
    }

    public function setCtrDestinoVo(NombreCentroText $vo): void
    {
        $this->ctr_destino = $vo->value();
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservText
    {
        return ObservText::fromNullableString($this->observ);
    }

    public function setObservVo(?ObservText $vo = null): void
    {
        $this->observ = $vo?->value();
    }
}