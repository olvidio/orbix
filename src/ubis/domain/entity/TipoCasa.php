<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\{TipoCasaCode, TipoCasaName};

class TipoCasa
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código del Tipo de Casa
     */
    private TipoCasaCode $tipo_casa;
    /**
     * Nombre del Tipo de Casa
     */
    private ?TipoCasaName $nombre_tipo_casa= null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API

    public function getTipoCasaVo(): TipoCasaCode
    {
        return $this->tipo_casa;
    }

    public function setTipoCasaVo(TipoCasaCode $tipoCasa): void
    {
        $this->tipo_casa = $tipoCasa;
    }


    public function getTipo_casa(): string
    {
        return $this->tipo_casa->value();
    }


    public function setTipo_casa(string $tipo_casa): void
    {
        $tipo_casa = trim($tipo_casa);
        $this->tipo_casa = new TipoCasaCode($tipo_casa);
    }

    // VO API
    public function getNombreTipoCasaVo(): ?TipoCasaName
    {
        return $this->nombre_tipo_casa;
    }

    public function setNombreTipoCasaVo(?TipoCasaName $nombre = null): void
    {
        $this->nombre_tipo_casa = $nombre;
    }


    public function getNombre_tipo_casa(): ?string
    {
        return $this->nombre_tipo_casa?->value();
    }


    public function setNombre_tipo_casa(?string $nombre_tipo_casa = null): void
    {
        $this->nombre_tipo_casa = TipoCasaName::fromNullableString($nombre_tipo_casa);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'tipo_casa';
    }

    public function getDatosCampos(): array
    {
        $oTipoDeCasaSet = new Set();

        $oTipoDeCasaSet->add($this->getDatosTipo_casa());
        $oTipoDeCasaSet->add($this->getDatosNombre_tipo_casa());
        return $oTipoDeCasaSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo tipo_casa de TipoDeCasa
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosTipo_casa(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_casa');
        $oDatosCampo->setMetodoGet('getTipo_casa');
        $oDatosCampo->setMetodoSet('setTipo_casa');
        $oDatosCampo->setEtiqueta(_("tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo nombre_tipo_casa de TipoDeCasa
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_tipo_casa(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_tipo_casa');
        $oDatosCampo->setMetodoGet('getNombre_tipo_casa');
        $oDatosCampo->setMetodoSet('setNombre_tipo_casa');
        $oDatosCampo->setEtiqueta(_("nombre del tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}