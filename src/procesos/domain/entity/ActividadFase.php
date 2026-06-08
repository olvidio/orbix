<?php

namespace src\procesos\domain\entity;

use src\shared\domain\DatosCampo;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\procesos\domain\value_objects\FaseId;
use src\shared\domain\traits\Hydratable;


class ActividadFase
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private FaseId $id_fase;

    private ?string $desc_fase = null;

    private bool $sf;

    private bool $sv;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated Use getIdFaseVo() instead
     */
    public function getId_fase(): int
    {
        return $this->id_fase->value();
    }

    /**
     * @deprecated Use setIdFaseVo() instead
     */
    public function setId_fase(int $id_fase): void
    {
        $this->id_fase = (FaseId::fromNullableInt($id_fase) ?? throw new \InvalidArgumentException('id_fase cannot be null'));
    }

    public function getIdFaseVo(): FaseId
    {
        return $this->id_fase;
    }

    public function setIdFaseVo(FaseId|int|null $id_fase): void
    {
        $this->id_fase = $id_fase instanceof FaseId
            ? $id_fase
            : (FaseId::fromNullableInt($id_fase) ?? throw new \InvalidArgumentException('id_fase cannot be null'));
    }

    public function getDesc_fase(): ?string
    {
        return $this->desc_fase;
    }

    public function setDesc_fase(?string $desc_fase = null): void
    {
        $this->desc_fase = $desc_fase;
    }


    public function isSf(): bool
    {
        return $this->sf;
    }

    public function setSf(bool $sf): void
    {
        $this->sf = $sf;
    }


    public function isSv(): bool
    {
        return $this->sv;
    }

    public function setSv(bool $sv): void
    {
        $this->sv = $sv;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_fase';
    }

    /** @return list<DatosCampo> */
    public function getDatosCampos(): array
    {
        return [
            $this->getDatosDesc_fase(),
            $this->getDatosSf(),
            $this->getDatosSv(),
        ];
    }

    /**
     * Recupera las propiedades del atributo desc_fase de ActividadFase
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosDesc_fase(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_fase');
        $oDatosCampo->setMetodoGet('getDesc_fase');
        $oDatosCampo->setMetodoSet('setDesc_fase');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo sf de ActividadFase
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosSf(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sf');
        $oDatosCampo->setMetodoGet('isSf');
        $oDatosCampo->setMetodoSet('setSf');
        $oDatosCampo->setEtiqueta(_("sf"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo sv de ActividadFase
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosSv(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sv');
        $oDatosCampo->setMetodoGet('isSv');
        $oDatosCampo->setMetodoSet('setSv');
        $oDatosCampo->setEtiqueta(_("sv"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}