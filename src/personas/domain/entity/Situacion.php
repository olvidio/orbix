<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\personas\domain\value_objects\{SituacionCode, SituacionName};
use src\shared\domain\traits\Hydratable;


class Situacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private SituacionCode $situacion;

    private ?SituacionName $nombre_situacion = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getSituacionVo(): SituacionCode
    {
        return $this->situacion;
    }

    public function setSituacionVo(SituacionCode|string|null $codigo): void
    {
        $this->situacion = $codigo instanceof SituacionCode
            ? $codigo
            : SituacionCode::fromNullableString($codigo);
    }

    public function getNombreSituacionVo(): ?SituacionName
    {
        return $this->nombre_situacion;
    }

    public function setNombreSituacionVo(SituacionName|string|null $texto = null): void
    {
        $this->nombre_situacion = $texto instanceof SituacionName
            ? $texto
            : SituacionName::fromNullableString($texto);
    }


    public function getSituacion(): string
    {
        return $this->situacion->value();
    }


    public function setSituacion(string $situacion): void
    {
        $situacion = trim($situacion);
        $this->situacion = new SituacionCode($situacion);
    }


    public function getNombre_situacion(): ?string
    {
        return $this->nombre_situacion?->value();
    }


    public function setNombre_situacion(?string $nombre_situacion = null): void
    {
        $this->nombre_situacion = SituacionName::fromNullableString($nombre_situacion);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'situacion';
    }

    public function getDatosCampos(): array
    {
        $oSituacionSet = new Set();

        $oSituacionSet->add($this->getDatosSituacion());
        $oSituacionSet->add($this->getDatosNombre_situacion());
        return $oSituacionSet->getTot();
    }

    private function getDatosSituacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('situacion');
        $oDatosCampo->setMetodoGet('getSituacionVo');
        $oDatosCampo->setMetodoSet('setSituacion');
        $oDatosCampo->setEtiqueta(_("situacion"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo nombre_situacion de Situacion
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_situacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_situacion');
        $oDatosCampo->setMetodoGet('getNombreSituacionVo');
        $oDatosCampo->setMetodoSet('setNombre_situacion');
        $oDatosCampo->setEtiqueta(_("nombre situación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(60);
        return $oDatosCampo;
    }
}