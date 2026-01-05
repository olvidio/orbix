<?php

namespace src\actividadtarifas\domain\entity;

use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\SfsvId;

class TipoTarifa
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_tarifa;

    private int $modo;

    private string|null $letra = null;

    private int|null $sfsv = null;

    private string|null $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/



    /**
     * @deprecated Usar getIdTarifaVo(): TarifaId
     */
    public function getId_tarifa(): int
    {
        return $this->id_tarifa;
    }


    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = $id_tarifa;
    }

    public function getIdTarifaVo(): TarifaId
    {
        return new TarifaId($this->id_tarifa);
    }

    public function setIdTarifaVo(TarifaId $id): void
    {
        $this->id_tarifa = $id->value();
    }


    /**
     * @deprecated Usar getModoVo(): TarifaModoId
     */
    public function getModo(): int
    {
        return $this->modo;
    }


    /**
     * @deprecated Usar setModoVo(TarifaModoId $id): void
     */
    public function setModo(int $modo): void
    {
        $this->modo = $modo;
    }

    public function getModoVo(): TarifaModoId
    {
        return new TarifaModoId($this->modo);
    }

    public function setModoVo(TarifaModoId $id): void
    {
        $this->modo = $id->value();
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
        return $this->letra;
    }


    /**
     * @deprecated Usar setLetraVo(?TarifaLetraCode $letra = null): void
     */
    public function setLetra(?string $letra = null): void
    {
        $this->letra = $letra;
    }

    public function getLetraVo(): ?TarifaLetraCode
    {
        if ($this->letra === null || $this->letra === '') {
            return null;
        }
        return new TarifaLetraCode($this->letra);
    }

    public function setLetraVo(?TarifaLetraCode $letra = null): void
    {
        $this->letra = $letra?->value();
    }


    /**
     * @deprecated Usar getSfsvVo(): ?SfsvId
     */
    public function getSfsv(): ?int
    {
        return $this->sfsv;
    }


    /**
     * @deprecated Usar setSfsvVo(?SfsvId $id = null): void
     */
    public function setSfsv(?int $sfsv = null): void
    {
        $this->sfsv = $sfsv;
    }

    public function getSfsvVo(): ?SfsvId
    {
        if ($this->sfsv === null) {
            return null;
        }
        return new SfsvId($this->sfsv);
    }

    public function setSfsvVo(?SfsvId $id = null): void
    {
        $this->sfsv = $id?->value();
    }


    public function getObserv(): ?string
    {
        return $this->observ;
    }


    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }
}