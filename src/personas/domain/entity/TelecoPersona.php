<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\value_objects\{NumTelecoText, ObservTelecoText};

class TelecoPersona
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_nom;

    private int $id_item;

    private int $id_tipo_teleco;

    private NumTelecoText $num_teleco;

    private ?ObservTelecoText $observ = null;

    private ?int $id_desc_teleco = null;

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

    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco;
    }

    public function setId_tipo_teleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = $id_tipo_teleco;
    }

    /**
     * @deprecated use getNumTelecoVo()
     */
    public function getNum_teleco(): string
    {
        return $this->num_teleco->value();
    }
    /**
     * @deprecated use setNumTelecoVo()
     */
    public function setNum_teleco(string $num_teleco): void
    {
        $this->num_teleco = NumTelecoText::fromNullableString($num_teleco);
    }
    public function getNumTelecoVo(): NumTelecoText
    {
        return $this->num_teleco;
    }
    public function setNumTelecoVo(NumTelecoText|string|null $texto): void
    {
        $this->num_teleco = $texto instanceof   NumTelecoText
            ? $texto
            : NumTelecoText::fromNullableString($texto);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }
    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservTelecoText::fromNullableString($observ);
    }
    public function getObservVo(): ?ObservTelecoText
    {
        return $this->observ;
    }

    public function setObservVo(ObservTelecoText|string|null $texto = null): void
    {
        $this->observ = $texto instanceof ObservTelecoText
            ? $texto
            : ObservTelecoText::fromNullableString($texto);
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