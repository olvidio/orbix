<?php

namespace src\ubis\domain\entity;
use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\{TipoCentroCode, TipoCentroName};

class TipoCentro
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código del Tipo de Centro
     */
    private TipoCentroCode $tipo_centro;
    /**
     * Nombre del Tipo de Centro
     */
    private ?TipoCentroName $nombre_tipo_centro = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getTipoCentroVo(): TipoCentroCode
    {
        return $this->tipo_centro;
    }

    public function setTipoCentroVo(?TipoCentroCode $tipoCentro = null): void
    {
        $this->tipo_centro = $tipoCentro;
    }


    public function getTipo_ctr(): string
    {
        return $this->tipo_centro?->value();
    }


    public function setTipo_ctr(string $tipo_ctr): void
    {
        $tipo_ctr = trim($tipo_ctr);
        $this->tipo_centro = $tipo_ctr !== '' ? new TipoCentroCode($tipo_ctr) : null;
    }

    // VO API
    public function getNombreTipoCentroVo(): ?TipoCentroName
    {
        return $this->nombre_tipo_centro;
    }

    public function setNombreTipoCentroVo(?TipoCentroName $nombre = null): void
    {
        $this->nombre_tipo_centro = $nombre;
    }


    public function getNombre_tipo_ctr(): ?string
    {
        return $this->nombre_tipo_centro?->value();
    }


    public function setNombre_tipo_ctr(?string $nombre_tipo_ctr = null): void
    {
        $this->nombre_tipo_centro = TipoCentroName::fromNullableString($nombre_tipo_ctr);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'tipo_ctr';
    }

    public function getDatosCampos(): array
    {
        $oTipoCentroSet = new Set();

        $oTipoCentroSet->add($this->getDatosTipo_ctr());
        $oTipoCentroSet->add($this->getDatosNombre_tipo_ctr());
        return $oTipoCentroSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo tipo_ctr de TipoCentro
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_ctr(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_ctr');
        $oDatosCampo->setMetodoGet('getTipo_ctr');
        $oDatosCampo->setMetodoSet('setTipo_ctr');
        $oDatosCampo->setEtiqueta(_("tipo de centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo nombre_tipo_ctr de TipoCentro
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_tipo_ctr(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_tipo_ctr');
        $oDatosCampo->setMetodoGet('getNombre_tipo_ctr');
        $oDatosCampo->setMetodoSet('setNombre_tipo_ctr');
        $oDatosCampo->setEtiqueta(_("nombre de tipo centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}