<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\EncargoDescText;
use src\encargossacd\domain\value_objects\EncargoOrden;
use src\encargossacd\domain\value_objects\EncargoPrioridad;
use src\encargossacd\domain\value_objects\EncargoTipoId;
use src\encargossacd\domain\value_objects\IdiomaCode;
use src\encargossacd\domain\value_objects\LugarDescText;
use src\encargossacd\domain\value_objects\ObservText;
use src\shared\domain\value_objects\SfsvId;

/**
 * Clase que implementa la entidad encargos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 23/12/2025
 */
class Encargo
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_enc de Encargo
     *
     * @var int
     */
    private int $iid_enc;
    /**
     * Id_tipo_enc de Encargo
     *
     * @var EncargoTipoId
     */
    private EncargoTipoId $iid_tipo_enc;
    /**
     * Sf_sv de Encargo
     *
     * @var SfsvId
     */
    private SfsvId $isf_sv;
    /**
     * Id_ubi de Encargo
     *
     * @var int|null
     */
    private int|null $iid_ubi = null;
    /**
     * Id_zona de Encargo
     *
     * @var int|null
     */
    private int|null $iid_zona = null;
    /**
     * Desc_enc de Encargo
     *
     * @var EncargoDescText|null
     */
    private EncargoDescText|null $sdesc_enc = null;
    /**
     * Idioma_enc de Encargo
     *
     * @var IdiomaCode|null
     */
    private IdiomaCode|null $sidioma_enc = null;
    /**
     * Desc_lugar de Encargo
     *
     * @var LugarDescText|null
     */
    private LugarDescText|null $sdesc_lugar = null;
    /**
     * Observ de Encargo
     *
     * @var ObservText|null
     */
    private ObservText|null $sobserv = null;
    /**
     * Orden de Encargo
     *
     * @var EncargoOrden|null
     */
    private EncargoOrden|null $iorden = null;
    /**
     * Prioridad de Encargo
     *
     * @var EncargoPrioridad|null
     */
    private EncargoPrioridad|null $iprioridad = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Encargo
     */
    public function setAllAttributes(array $aDatos): Encargo
    {
        if (array_key_exists('id_enc', $aDatos)) {
            $this->setId_enc($aDatos['id_enc']);
        }
        if (array_key_exists('id_tipo_enc', $aDatos)) {
            $this->setId_tipo_enc($aDatos['id_tipo_enc']);
        }
        if (array_key_exists('sf_sv', $aDatos)) {
            $this->setSf_sv($aDatos['sf_sv']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('id_zona', $aDatos)) {
            $this->setId_zona($aDatos['id_zona']);
        }
        if (array_key_exists('desc_enc', $aDatos)) {
            $this->setDesc_enc($aDatos['desc_enc']);
        }
        if (array_key_exists('idioma_enc', $aDatos)) {
            $this->setIdioma_enc($aDatos['idioma_enc']);
        }
        if (array_key_exists('desc_lugar', $aDatos)) {
            $this->setDesc_lugar($aDatos['desc_lugar']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('orden', $aDatos)) {
            $this->setOrden($aDatos['orden']);
        }
        if (array_key_exists('prioridad', $aDatos)) {
            $this->setPrioridad($aDatos['prioridad']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_enc
     */
    public function getId_enc(): int
    {
        return $this->iid_enc;
    }

    /**
     *
     * @param int $iid_enc
     */
    public function setId_enc(int $iid_enc): void
    {
        $this->iid_enc = $iid_enc;
    }
    /**
     *
     * @return int $iid_tipo_enc
     */
    /**
     * @deprecated Usar `getTipo_encVo(): EncargoTipoId` en su lugar.
     */
    public function getId_tipo_enc(): int
    {
        return $this->iid_tipo_enc->value();
    }
    /**
     *
     * @param int $iid_tipo_enc
     */
    /**
     * @deprecated Usar `setTipo_encVo(EncargoTipoId $vo): void` en su lugar.
     */
    public function setId_tipo_enc(int $iid_tipo_enc): void
    {
        $this->iid_tipo_enc = new EncargoTipoId($iid_tipo_enc);
    }

    public function getTipo_encVo(): EncargoTipoId
    {
        return $this->iid_tipo_enc;
    }

    public function setTipo_encVo(EncargoTipoId $vo): void
    {
        $this->iid_tipo_enc = $vo;
    }

    /**
     *
     * @return int $isf_sv
     */
    /**
     * @deprecated Usar `getSf_svVo(): SfsvId` en su lugar.
     */
    public function getSf_sv(): int
    {
        return $this->isf_sv->value();
    }
    /**
     *
     * @param int $isf_sv
     */
    /**
     * @deprecated Usar `setSf_svVo(SfsvId $vo): void` en su lugar.
     */
    public function setSf_sv(int $isf_sv): void
    {
        $this->isf_sv = new SfsvId($isf_sv);
    }

    public function getSf_svVo(): SfsvId
    {
        return $this->isf_sv;
    }

    public function setSf_svVo(SfsvId $vo): void
    {
        $this->isf_sv = $vo;
    }

    /**
     *
     * @return int|null $iid_ubi
     */
    public function getId_ubi(): ?int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int|null $iid_ubi
     */
    public function setId_ubi(?int $iid_ubi = null): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return int|null $iid_zona
     */
    public function getId_zona(): ?int
    {
        return $this->iid_zona;
    }

    /**
     *
     * @param int|null $iid_zona
     */
    public function setId_zona(?int $iid_zona = null): void
    {
        $this->iid_zona = $iid_zona;
    }
    /**
     *
     * @return string|null $sdesc_enc
     */
    /**
     * @deprecated Usar `getDesc_encVo(): ?EncargoDescText` en su lugar.
     */
    public function getDesc_enc(): ?string
    {
        return $this->sdesc_enc?->value();
    }
    /**
     *
     * @param string|null $sdesc_enc
     */
    /**
     * @deprecated Usar `setDesc_encVo(?EncargoDescText $vo): void` en su lugar.
     */
    public function setDesc_enc(?string $sdesc_enc = null): void
    {
        $this->sdesc_enc = $sdesc_enc !== null ? new EncargoDescText($sdesc_enc) : null;
    }

    public function getDesc_encVo(): ?EncargoDescText
    {
        return $this->sdesc_enc;
    }

    public function setDesc_encVo(?EncargoDescText $vo): void
    {
        $this->sdesc_enc = $vo;
    }

    /**
     *
     * @return string|null $sidioma_enc
     */
    /**
     * @deprecated Usar `getIdioma_encVo(): ?IdiomaCode` en su lugar.
     */
    public function getIdioma_enc(): ?string
    {
        return $this->sidioma_enc?->value();
    }
    /**
     *
     * @param string|null $sidioma_enc
     */
    /**
     * @deprecated Usar `setIdioma_encVo(?IdiomaCode $vo): void` en su lugar.
     */
    public function setIdioma_enc(?string $sidioma_enc = null): void
    {
        $this->sidioma_enc = $sidioma_enc !== null ? new IdiomaCode($sidioma_enc) : null;
    }

    public function getIdioma_encVo(): ?IdiomaCode
    {
        return $this->sidioma_enc;
    }

    public function setIdioma_encVo(?IdiomaCode $vo): void
    {
        $this->sidioma_enc = $vo;
    }

    /**
     *
     * @return string|null $sdesc_lugar
     */
    /**
     * @deprecated Usar `getDesc_lugarVo(): ?LugarDescText` en su lugar.
     */
    public function getDesc_lugar(): ?string
    {
        return $this->sdesc_lugar?->value();
    }
    /**
     *
     * @param string|null $sdesc_lugar
     */
    /**
     * @deprecated Usar `setDesc_lugarVo(?LugarDescText $vo): void` en su lugar.
     */
    public function setDesc_lugar(?string $sdesc_lugar = null): void
    {
        $this->sdesc_lugar = $sdesc_lugar !== null ? new LugarDescText($sdesc_lugar) : null;
    }

    public function getDesc_lugarVo(): ?LugarDescText
    {
        return $this->sdesc_lugar;
    }

    public function setDesc_lugarVo(?LugarDescText $vo): void
    {
        $this->sdesc_lugar = $vo;
    }

    /**
     *
     * @return string|null $sobserv
     */
    /**
     * @deprecated Usar `getObservVo(): ?ObservText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->sobserv?->value();
    }
    /**
     *
     * @param string|null $sobserv
     */
    /**
     * @deprecated Usar `setObservVo(?ObservText $vo): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv !== null ? new ObservText($sobserv) : null;
    }

    public function getObservVo(): ?ObservText
    {
        return $this->sobserv;
    }

    public function setObservVo(?ObservText $vo): void
    {
        $this->sobserv = $vo;
    }

    /**
     *
     * @return int|null $iorden
     */
    /**
     * @deprecated Usar `getOrdenVo(): ?EncargoOrden` en su lugar.
     */
    public function getOrden(): ?int
    {
        return $this->iorden?->value();
    }
    /**
     *
     * @param int|null $iorden
     */
    /**
     * @deprecated Usar `setOrdenVo(?EncargoOrden $vo): void` en su lugar.
     */
    public function setOrden(?int $iorden = null): void
    {
        $this->iorden = $iorden !== null ? new EncargoOrden($iorden) : null;
    }

    public function getOrdenVo(): ?EncargoOrden
    {
        return $this->iorden;
    }

    public function setOrdenVo(?EncargoOrden $vo): void
    {
        $this->iorden = $vo;
    }

    /**
     *
     * @return int|null $iprioridad
     */
    /**
     * @deprecated Usar `getPrioridadVo(): ?EncargoPrioridad` en su lugar.
     */
    public function getPrioridad(): ?int
    {
        return $this->iprioridad?->value();
    }
    /**
     *
     * @param int|null $iprioridad
     */
    /**
     * @deprecated Usar `setPrioridadVo(?EncargoPrioridad $vo): void` en su lugar.
     */
    public function setPrioridad(?int $iprioridad = null): void
    {
        $this->iprioridad = $iprioridad !== null ? new EncargoPrioridad($iprioridad) : null;
    }

    public function getPrioridadVo(): ?EncargoPrioridad
    {
        return $this->iprioridad;
    }

    public function setPrioridadVo(?EncargoPrioridad $vo): void
    {
        $this->iprioridad = $vo;
    }
}