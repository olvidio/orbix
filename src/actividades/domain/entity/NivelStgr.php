<?php

namespace src\actividades\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\value_objects\{NivelStgrId, NivelStgrDesc, NivelStgrBreve, NivelStgrOrden};
use src\shared\domain\traits\Hydratable;

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
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Nivel_stgr de NivelStgr
     */
    private NivelStgrId $nivel_stgr;
    /**
     * Desc_nivel de NivelStgr
     */
    private NivelStgrDesc $desc_nivel;
    /**
     * Desc_breve de NivelStgr
     */
    private ?NivelStgrBreve $desc_breve = null;
    /**
     * Orden de NivelStgr
     */
    private ?NivelStgrOrden $orden = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated usar getId()
     */
    public function getNivel_stgr(): int
    {
        return $this->nivel_stgr->value();
    }

    /**
     *
     * @param int $inivel_stgr
     */
    /**
     * @deprecated usar setId(NivelStgrId $id)
     */
    public function setNivel_stgr(int $nivel_stgr): void
    {
        $this->nivel_stgr = new NivelStgrId($nivel_stgr);
    }

    // Nuevos métodos con Value Objects
    public function getId(): NivelStgrId
    {
        return $this->nivel_stgr;
    }

    public function setId(NivelStgrId $id): void
    {
        $this->nivel_stgr = $id;
    }

    /**
     *
     * @return string $desc_nivel
     */
    /**
     * @deprecated usar getDescNivelVo()
     */
    public function getDesc_nivel(): string
    {
        return $this->desc_nivel->value();
    }

    /**
     *
     * @param string $sdesc_nivel
     */
    /**
     * @deprecated usar setDescNivelVo(NivelStgrDesc $desc)
     */
    public function setDesc_nivel(string $desc_nivel): void
    {
        $this->desc_nivel = new NivelStgrDesc($desc_nivel);
    }

    public function getDescNivelVo(): NivelStgrDesc
    {
        return $this->desc_nivel;
    }

    public function setDescNivelVo(NivelStgrDesc|string|null $texto): void
    {
        $this->desc_nivel = $texto instanceof NivelStgrDesc
            ? $texto
            : NivelStgrBreve::fromNullableString($texto);
    }

    /**
     *
     * @return string|null $desc_breve
     */
    /**
     * @deprecated usar getDescBreveVo()
     */
    public function getDesc_breve(): ?string
    {
        return $this->desc_breve?->value();
    }

    /**
     *
     * @param string|null $sdesc_breve
     */
    /**
     * @deprecated usar setDescBreveVo(?NivelStgrBreve $breve)
     */
    public function setDesc_breve(?string $desc_breve = null): void
    {
        $this->desc_breve = NivelStgrBreve::fromNullableString($desc_breve);
    }

    public function getDescBreveVo(): ?NivelStgrBreve
    {
        return $this->desc_breve;
    }

    public function setDescBreveVo(NivelStgrBreve|string|null $texto = null): void
    {
        $this->desc_breve = $texto instanceof NivelStgrBreve
            ? $texto
            : NivelStgrBreve::fromNullableString($texto);
    }

    /**
     *
     * @return int|null $orden
     */
    /**
     * @deprecated usar getOrdenVo()
     */
    public function getOrden(): ?int
    {
        return $this->orden?->value();
    }

    /**
     *
     * @param int|null $iorden
     */
    /**
     * @deprecated usar setOrdenVo(?NivelStgrOrden $orden)
     */
    public function setOrden(?int $orden = null): void
    {
        $this->orden = NivelStgrOrden::fromNullable($orden);
    }

    public function getOrdenVo(): ?NivelStgrOrden
    {
        return $this->orden;
    }

    public function setOrdenVo(NivelStgrOrden|int|null $valor = null): void
    {
        $this->orden = $valor instanceof NivelStgrOrden
            ? $valor
            : NivelStgrOrden::fromNullable($valor);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'nivel_stgr';
    }

    public function getDatosCampos(): array
    {
        $oNivelStgrSet = new Set();

        $oNivelStgrSet->add($this->getDatosDesc_nivel());
        $oNivelStgrSet->add($this->getDatosDesc_breve());
        $oNivelStgrSet->add($this->getDatosOrden());
        return $oNivelStgrSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo desc_nivel de NivelStgr
     * en una clase del tipo DatosCampo
     *
     */
    private function getDatosDesc_nivel(): DatosCampo
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
     * Recupera las propiedades del atributo desc_breve de NivelStgr
     * en una clase del tipo DatosCampo
     *
     */
    private function getDatosDesc_breve(): DatosCampo
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
     * Recupera las propiedades del atributo orden de NivelStgr
     * en una clase del tipo DatosCampo
     *
     */
    private function getDatosOrden(): DatosCampo
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