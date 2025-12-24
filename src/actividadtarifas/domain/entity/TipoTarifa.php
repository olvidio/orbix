<?php

namespace src\actividadtarifas\domain\entity;

use src\actividadtarifas\domain\value_objects\TarifaLetraCode;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use src\shared\domain\value_objects\SfsvId;

/**
 * Clase que implementa la entidad xa_tipo_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class TipoTarifa
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tarifa de TipoTarifa
     *
     * @var int
     */
    private int $iid_tarifa;
    /**
     * Modo de TipoTarifa
     *
     * @var int
     */
    private int $imodo;
    /**
     * Letra de TipoTarifa
     *
     * @var string|null
     */
    private string|null $sletra = null;
    /**
     * Sfsv de TipoTarifa
     *
     * @var int|null
     */
    private int|null $isfsv = null;
    /**
     * Observ de TipoTarifa
     *
     * @var string|null
     */
    private string|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoTarifa
     */
    public function setAllAttributes(array $aDatos): TipoTarifa
    {
        if (array_key_exists('id_tarifa', $aDatos)) {
            $val = $aDatos['id_tarifa'];
            if ($val instanceof TarifaId) {
                $this->setIdTarifaVo($val);
            } else {
                $this->setId_tarifa($val);
            }
        }
        if (array_key_exists('modo', $aDatos)) {
            $val = $aDatos['modo'];
            if ($val instanceof TarifaModoId) {
                $this->setModoVo($val);
            } else {
                $this->setModo($val);
            }
        }
        if (array_key_exists('letra', $aDatos)) {
            $val = $aDatos['letra'];
            if ($val instanceof TarifaLetraCode || $val === null) {
                $this->setLetraVo($val);
            } else {
                $this->setLetra($val);
            }
        }
        if (array_key_exists('sfsv', $aDatos)) {
            $val = $aDatos['sfsv'];
            if ($val instanceof SfsvId || $val === null) {
                $this->setSfsvVo($val);
            } else {
                $this->setSfsv($val);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_tarifa
     */
    /**
     * @deprecated Usar getIdTarifaVo(): TarifaId
     */
    public function getId_tarifa(): int
    {
        return $this->iid_tarifa;
    }

    /**
     *
     * @param int $iid_tarifa
     */
    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $iid_tarifa): void
    {
        $this->iid_tarifa = $iid_tarifa;
    }

    public function getIdTarifaVo(): TarifaId
    {
        return new TarifaId($this->iid_tarifa);
    }

    public function setIdTarifaVo(TarifaId $id): void
    {
        $this->iid_tarifa = $id->value();
    }

    /**
     *
     * @return int $imodo
     */
    /**
     * @deprecated Usar getModoVo(): TarifaModoId
     */
    public function getModo(): int
    {
        return $this->imodo;
    }

    /**
     *
     * @param int $imodo
     */
    /**
     * @deprecated Usar setModoVo(TarifaModoId $id): void
     */
    public function setModo(int $imodo): void
    {
        $this->imodo = $imodo;
    }

    public function getModoVo(): TarifaModoId
    {
        return new TarifaModoId($this->imodo);
    }

    public function setModoVo(TarifaModoId $id): void
    {
        $this->imodo = $id->value();
    }

    public function getModoTxt():string
    {
        $a_modos = TarifaModoId::getArrayModo();

        return $a_modos($this->imodo);
    }

    /**
     *
     * @return string|null $sletra
     */
    /**
     * @deprecated Usar getLetraVo(): ?TarifaLetraCode
     */
    public function getLetra(): ?string
    {
        return $this->sletra;
    }

    /**
     *
     * @param string|null $sletra
     */
    /**
     * @deprecated Usar setLetraVo(?TarifaLetraCode $letra = null): void
     */
    public function setLetra(?string $sletra = null): void
    {
        $this->sletra = $sletra;
    }

    public function getLetraVo(): ?TarifaLetraCode
    {
        if ($this->sletra === null || $this->sletra === '') {
            return null;
        }
        return new TarifaLetraCode($this->sletra);
    }

    public function setLetraVo(?TarifaLetraCode $letra = null): void
    {
        $this->sletra = $letra?->value();
    }

    /**
     *
     * @return int|null $isfsv
     */
    /**
     * @deprecated Usar getSfsvVo(): ?SfsvId
     */
    public function getSfsv(): ?int
    {
        return $this->isfsv;
    }

    /**
     *
     * @param int|null $isfsv
     */
    /**
     * @deprecated Usar setSfsvVo(?SfsvId $id = null): void
     */
    public function setSfsv(?int $isfsv = null): void
    {
        $this->isfsv = $isfsv;
    }

    public function getSfsvVo(): ?SfsvId
    {
        if ($this->isfsv === null) {
            return null;
        }
        return new SfsvId($this->isfsv);
    }

    public function setSfsvVo(?SfsvId $id = null): void
    {
        $this->isfsv = $id?->value();
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }
}