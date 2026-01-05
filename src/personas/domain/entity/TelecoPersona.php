<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\value_objects\{NumTelecoText, ObservTelecoText, TipoTelecoCode};

class TelecoPersona
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_nom;

    private int $id_item;

    private int $id_tipo_teleco;

    private string $num_teleco;

    private string|null $observ = null;

    private int|null $id_desc_teleco = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }

    /**
     * @deprecated use getTipoTelecoVo()
     */
    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco;
    }

    /**
     * @deprecated use setTipoTelecoVo()
     */
    public function setId_tipo_teleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = $id_tipo_teleco;
    }

    /**
     * API VO para id_tipo_teleco (código): TipoTelecoCode
     */
    public function getTipoTelecoVo(): TipoTelecoCode
    {
        return new TipoTelecoCode($this->id_tipo_teleco);
    }

    public function setTipoTelecoVo(TipoTelecoCode $code): void
    {
        $this->id_tipo_teleco = $code->value();
    }

    /**
     * @deprecated use getNumTelecoVo()
     */
    public function getNum_teleco(): string
    {
        return $this->num_teleco;
    }

    /**
     * @deprecated use setNumTelecoVo()
     */
    public function setNum_teleco(string $num_teleco): void
    {
        $this->num_teleco = $num_teleco;
    }

    public function getNumTelecoVo(): NumTelecoText
    {
        return new NumTelecoText($this->num_teleco);
    }

    public function setNumTelecoVo(NumTelecoText $texto): void
    {
        $this->num_teleco = $texto->value();
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservTelecoText
    {
        return ObservTelecoText::fromNullableString($this->observ);
    }

    public function setObservVo(?ObservTelecoText $texto = null): void
    {
        $this->observ = $texto?->value();
    }


    public function getId_desc_teleco(): ?int
    {
        return $this->id_desc_teleco;
    }


    public function setId_desc_teleco(?int $id_desc_teleco = null): void
    {
        $this->id_desc_teleco = $id_desc_teleco;
    }

    // Nota: En TelecoPersona, id_desc_teleco es un id (int|null). Si en UI se maneja como texto,
    // la conversión a texto debe realizarse en la capa repositorio de descripciones.

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }

    public function getDatosCampos(): array
    {
        $oSet = new Set();
        $oSet->add($this->getDatosId_nom());
        $oSet->add($this->getDatosId_tipo_teleco());
        $oSet->add($this->getDatosId_desc_teleco());
        $oSet->add($this->getDatosNum_teleco());
        $oSet->add($this->getDatosObserv());
        return $oSet->getTot();
    }

    private function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    private function getDatosId_tipo_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getId_tipo_teleco');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoTelecoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNombreTelecoVo');
        $oDatosCampo->setArgument3('getArrayTiposTelecoPersona');
        $oDatosCampo->setAccion('id_desc_teleco');
        return $oDatosCampo;
    }

    private function getDatosId_desc_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_desc_teleco');
        $oDatosCampo->setMetodoGet('getId_desc_teleco');
        $oDatosCampo->setMetodoSet('setId_desc_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument(DescTelecoRepositoryInterface::class);
        $oDatosCampo->setArgument2('getDescTelecoVo');
        $oDatosCampo->setArgument3('getArrayDescTelecoPersonas');
        $oDatosCampo->setDepende('id_tipo_teleco');
        return $oDatosCampo;
    }

    private function getDatosNum_teleco(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_teleco');
        $oDatosCampo->setMetodoGet('getNum_teleco');
        $oDatosCampo->setMetodoSet('setNum_teleco');
        $oDatosCampo->setEtiqueta(_("número o siglas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    private function getDatosObserv(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ');
        $oDatosCampo->setMetodoGet('getObserv');
        $oDatosCampo->setMetodoSet('setObserv');
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }
}