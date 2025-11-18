<?php

namespace src\actividades\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\value_objects\{NivelStgrId, NivelStgrDesc, NivelStgrBreve, NivelStgrOrden};

/**
 * Clase que implementa la entidad xa_nivel_stgr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class NivelStgr
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Nivel_stgr de NivelStgr
     */
    private NivelStgrId $inivel_stgr;
    /**
     * Desc_nivel de NivelStgr
     */
    private NivelStgrDesc $sdesc_nivel;
    /**
     * Desc_breve de NivelStgr
     */
    private ?NivelStgrBreve $sdesc_breve = null;
    /**
     * Orden de NivelStgr
     */
    private ?NivelStgrOrden $iorden = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return NivelStgr
     */
    public function setAllAttributes(array $aDatos): NivelStgr
    {
        if (array_key_exists('nivel_stgr', $aDatos)) {
            $val = $aDatos['nivel_stgr'];
            if ($val instanceof NivelStgrId) {
                $this->setId($val);
            } else {
                $this->setNivel_stgr((int)$val);
            }
        }
        if (array_key_exists('desc_nivel', $aDatos)) {
            $val = $aDatos['desc_nivel'];
            if ($val instanceof NivelStgrDesc) {
                $this->setDescNivelVO($val);
            } else {
                $this->setDesc_nivel((string)$val);
            }
        }
        if (array_key_exists('desc_breve', $aDatos)) {
            $val = $aDatos['desc_breve'];
            if ($val instanceof NivelStgrBreve || $val === null) {
                $this->setDescBreveVO($val);
            } else {
                $this->setDesc_breve($val === '' ? null : (string)$val);
            }
        }
        if (array_key_exists('orden', $aDatos)) {
            $val = $aDatos['orden'];
            if ($val instanceof NivelStgrOrden || $val === null) {
                $this->setOrdenVO($val);
            } else {
                $this->setOrden($val === '' ? null : ($val === null ? null : (int)$val));
            }
        }
        return $this;
    }

    /**
     *
     * @return int $inivel_stgr
     */
    /**
     * @deprecated usar getId()
     */
    public function getNivel_stgr(): int
    {
        return $this->inivel_stgr->value();
    }

    /**
     *
     * @param int $inivel_stgr
     */
    /**
     * @deprecated usar setId(NivelStgrId $id)
     */
    public function setNivel_stgr(int $inivel_stgr): void
    {
        $this->inivel_stgr = new NivelStgrId($inivel_stgr);
    }

    // Nuevos métodos con Value Objects
    public function getId(): NivelStgrId
    {
        return $this->inivel_stgr;
    }

    public function setId(NivelStgrId $id): void
    {
        $this->inivel_stgr = $id;
    }

    /**
     *
     * @return string $sdesc_nivel
     */
    /**
     * @deprecated usar getDescNivelVO()
     */
    public function getDesc_nivel(): string
    {
        return $this->sdesc_nivel->value();
    }

    /**
     *
     * @param string $sdesc_nivel
     */
    /**
     * @deprecated usar setDescNivelVO(NivelStgrDesc $desc)
     */
    public function setDesc_nivel(string $sdesc_nivel): void
    {
        $this->sdesc_nivel = new NivelStgrDesc($sdesc_nivel);
    }

    public function getDescNivelVO(): NivelStgrDesc
    {
        return $this->sdesc_nivel;
    }

    public function setDescNivelVO(NivelStgrDesc $desc): void
    {
        $this->sdesc_nivel = $desc;
    }

    /**
     *
     * @return string|null $sdesc_breve
     */
    /**
     * @deprecated usar getDescBreveVO()
     */
    public function getDesc_breve(): ?string
    {
        return $this->sdesc_breve?->value();
    }

    /**
     *
     * @param string|null $sdesc_breve
     */
    /**
     * @deprecated usar setDescBreveVO(?NivelStgrBreve $breve)
     */
    public function setDesc_breve(?string $sdesc_breve = null): void
    {
        $this->sdesc_breve = NivelStgrBreve::fromNullableString($sdesc_breve);
    }

    public function getDescBreveVO(): ?NivelStgrBreve
    {
        return $this->sdesc_breve;
    }

    public function setDescBreveVO(?NivelStgrBreve $breve = null): void
    {
        $this->sdesc_breve = $breve;
    }

    /**
     *
     * @return int|null $iorden
     */
    /**
     * @deprecated usar getOrdenVO()
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
     * @deprecated usar setOrdenVO(?NivelStgrOrden $orden)
     */
    public function setOrden(?int $iorden = null): void
    {
        $this->iorden = NivelStgrOrden::fromNullable($iorden);
    }

    public function getOrdenVO(): ?NivelStgrOrden
    {
        return $this->iorden;
    }

    public function setOrdenVO(?NivelStgrOrden $orden = null): void
    {
        $this->iorden = $orden;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'nivel_stgr';
    }

    function getDatosCampos()
    {
        $oNivelStgrSet = new Set();

        $oNivelStgrSet->add($this->getDatosDesc_nivel());
        $oNivelStgrSet->add($this->getDatosDesc_breve());
        $oNivelStgrSet->add($this->getDatosOrden());
        return $oNivelStgrSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sdesc_nivel de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosDesc_nivel()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_nivel');
        $oDatosCampo->setMetodoGet('getDesc_nivel'); // legacy para UI
        $oDatosCampo->setMetodoSet('setDesc_nivel'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("descripción nivel"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_breve de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosDesc_breve()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_breve');
        $oDatosCampo->setMetodoGet('getDesc_breve'); // legacy para UI
        $oDatosCampo->setMetodoSet('setDesc_breve'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("breve"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(2);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosOrden()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden'); // legacy para UI
        $oDatosCampo->setMetodoSet('setOrden'); // legacy para UI
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(3);
        return $oDatosCampo;
    }
}