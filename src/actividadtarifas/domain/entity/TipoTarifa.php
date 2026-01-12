<?php

namespace src\actividadtarifas\domain\entity;

use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\SfsvId;
use src\ubis\domain\value_objects\ObservCasaText;

class TipoTarifa
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private TarifaId $id_tarifa;

    private TarifaModoId $modo;

    private ?TarifaLetraCode $letra = null;

    private ?SfsvId $sfsv = null;

    private ?ObservCasaText $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/



    /**
     * @deprecated Usar getIdTarifaVo(): TarifaId
     */
    public function getId_tarifa(): int
    {
        return $this->id_tarifa->value();
    }


    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = TarifaId::fromNullableInt($id_tarifa);
    }

    public function getIdTarifaVo(): TarifaId
    {
        return $this->id_tarifa;
    }

    public function setIdTarifaVo(TarifaId|int|null $id): void
    {
        $this->id_tarifa = $id instanceof TarifaId
            ? $id
            : TarifaId::fromNullableInt($id);
    }


    /**
     * @deprecated Usar getModoVo(): TarifaModoId
     */
    public function getModo(): int
    {
        return $this->modo->value();
    }


    /**
     * @deprecated Usar setModoVo(TarifaModoId $id): void
     */
    public function setModo(int $modo): void
    {
        $this->modo = TarifaModoId::fromNullableInt($modo);
    }

    public function getModoVo(): TarifaModoId
    {
        return $this->modo;
    }

    public function setModoVo(TarifaModoId|int|null $id): void
    {
        $this->modo = $id instanceof TarifaModoId
            ? $id
            : TarifaModoId::fromNullableInt($id);
    }

    public function getModoTxt():string
    {
        $a_modos = TarifaModoId::getArrayModo();

        return $a_modos($this->modo);
    }


    /**
     * @deprecated Usar getLetraVo(): ?TarifaLetraCode
     */
    public function getLetra(): ?string
    {
        return $this->letra?->value();
    }


    /**
     * @deprecated Usar setLetraVo(?TarifaLetraCode $letra = null): void
     */
    public function setLetra(?string $letra = null): void
    {
        $this->letra = TarifaLetraCode::fromNullableString($letra);
    }

    public function getLetraVo(): ?TarifaLetraCode
    {
        return $this->letra;
    }

    public function setLetraVo(TarifaLetraCode|string|null $letra = null): void
    {
        $this->letra = $letra instanceof TarifaLetraCode
            ? $letra
            : TarifaLetraCode::fromNullableString($letra);
    }


    /**
     * @deprecated Usar getSfsvVo(): ?SfsvId
     */
    public function getSfsv(): ?int
    {
        return $this->sfsv?->value();
    }


    /**
     * @deprecated Usar setSfsvVo(?SfsvId $id = null): void
     */
    public function setSfsv(?int $sfsv = null): void
    {
        $this->sfsv = SfsvId::fromNullableInt($sfsv);
    }

    public function getSfsvVo(): ?SfsvId
    {
        return $this->sfsv;
    }

    public function setSfsvVo(SfsvId|int $id = null): void
    {
        $this->sfsv = $id instanceof SfsvId
            ? $id
            : SfsvId::fromNullableInt($id);
    }

    /**
     * @deprecated
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }


    /**
     * @deprecated
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservCasaText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservCasaText
    {
        return $this->observ;
    }

    public function setObservVo(ObservCasaText|string|null $observ = null): void
    {
        $this->observ = $observ instanceof ObservCasaText
            ? $observ
            : ObservCasaText::fromNullableString($observ);
    }
}