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
    private TipoCentroCode $tipo_ctr;
    /**
     * Nombre del Tipo de Centro
     */
    private ?TipoCentroName $nombre_tipo_ctr = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getTipoCtrVo(): TipoCentroCode
    {
        return $this->tipo_ctr;
    }

    public function setTipoCtrVo(TipoCentroCode|string|null $tipoCentro = null): void
    {
        $this->tipo_ctr = $tipoCentro instanceof TipoCentroCode
            ? $tipoCentro
            : TipoCentroCode::fromNullableString($tipoCentro);
    }


    public function getTipo_ctr(): string
    {
        return $this->tipo_ctr->value();
    }


    public function setTipo_ctr(string $tipo_ctr): void
    {
        $this->tipo_ctr = TipoCentroCode::fromNullableString($tipo_ctr);
    }

    // VO API
    public function getNombreTipoCtrVo(): ?TipoCentroName
    {
        return $this->nombre_tipo_ctr;
    }

    public function setNombreTipoCtrVo(TipoCentroName|string|null $texto = null): void
    {
        $this->nombre_tipo_ctr = $texto instanceof TipoCentroName
            ? $texto
            : TipoCentroName::fromNullableString($texto);
    }


    public function getNombre_tipo_ctr(): ?string
    {
        return $this->nombre_tipo_ctr?->value();
    }


    public function setNombre_tipo_ctr(?string $nombre_tipo_ctr = null): void
    {
        $this->nombre_tipo_ctr = TipoCentroName::fromNullableString($nombre_tipo_ctr);
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