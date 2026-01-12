<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CongresoName;
use src\profesores\domain\value_objects\CongresoTipo;
use src\profesores\domain\value_objects\LugarName;
use src\profesores\domain\value_objects\OrganizaName;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;


class ProfesorCongreso
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private CongresoName $congreso;

    private ?LugarName $lugar = null;

    private ?DateTimeLocal $f_ini = null;

    private ?DateTimeLocal $f_fin = null;

    private ?OrganizaName $organiza = null;

    private ?CongresoTipo $tipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getCongresoVo(): CongresoName
    {
        return $this->congreso;
    }

    public function setCongresoVo(CongresoName|string $valor = null): void
    {
        $this->congreso = $valor instanceof CongresoName
            ? $valor
            : CongresoName::fromNullableString($valor);
    }

    /**
     * @deprecated use getCongresoVo()
     */
    public function getCongreso(): string
    {
        return $this->congreso->value();
    }

    /**
     * @deprecated use setCongresoVo()
     */
    public function setCongreso(string $valor = null): void
    {
        $this->congreso = CongresoName::fromNullableString($valor);
    }

    public function getLugarVo(): ?LugarName
    {
        return $this->lugar;
    }

    public function setLugarVo(LugarName|string|null $valor = null): void
    {
        $this->lugar = $valor instanceof LugarName
            ? $valor
            : LugarName::fromNullableString($valor);
    }

    /**
     * @deprecated use getLugarVo()
     */
    public function getLugar(): ?string
    {
        return $this->lugar?->value();
    }

    /**
     * @deprecated use setLugarVo()
     */
    public function setLugar(?string $valor = null): void
    {
        $this->lugar = LugarName::fromNullableString($valor);
    }

    public function getOrganizaVo(): ?OrganizaName
    {
        return $this->organiza;
    }

    public function setOrganizaVo(OrganizaName|string|null $valor = null): void
    {
        $this->organiza = $valor instanceof OrganizaName
            ? $valor
            : OrganizaName::fromNullableString($valor);
    }

    /**
     * @deprecated use getOrganizaVo()
     */
    public function getOrganiza(): ?string
    {
        return $this->organiza?->value();
    }

    /**
     * @deprecated use setOrganizaVo()
     */
    public function setOrganiza(?string $valor = null): void
    {
        $this->organiza = OrganizaName::fromNullableString($valor);
    }

    public function getTipoCongresoVo(): ?CongresoTipo
    {
        return $this->tipo;
    }

    public function setTipoCongresoVo(CongresoTipo|string|null $valor = null): void
    {
        $this->tipo = $valor instanceof CongresoTipo
            ? $valor
            : CongresoTipo::fromNullableInt($valor);
    }

    /**
     * @deprecated use getTipoCongresoVo()
     */
    public function getTipoCongreso(): ?string
    {
        return $this->tipo?->value();
    }

    /**
     * @deprecated use setTipoCongresoVo()
     */
    public function setTipoCongreso(?string $valor = null): void
    {
        $this->tipo = CongresoTipo::fromNullableInt($valor);
    }

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $valor): void
    {
        $this->id_item = $valor;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $valor): void
    {
        $this->id_nom = $valor;
    }

    public function getF_ini(): ?DateTimeLocal
    {
        return $this->f_ini;
    }

    public function setF_ini(?DateTimeLocal $valor): void
    {
        $this->f_ini = $valor;
    }

    public function getF_fin(): ?DateTimeLocal
    {
        return $this->f_fin;
    }

    public function setF_fin(?DateTimeLocal $valor): void
    {
        $this->f_fin = $valor;
    }

/* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    public function getDatosCampos(): array
    {
        $oProfesorCongresoSet = new Set();

        $oProfesorCongresoSet->add($this->getDatosId_nom());
        $oProfesorCongresoSet->add($this->getDatosCongreso());
        $oProfesorCongresoSet->add($this->getDatosLugar());
        $oProfesorCongresoSet->add($this->getDatosF_ini());
        $oProfesorCongresoSet->add($this->getDatosF_fin());
        $oProfesorCongresoSet->add($this->getDatosOrganiza());
        $oProfesorCongresoSet->add($this->getDatosTipo());
        return $oProfesorCongresoSet->getTot();
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

    private function getDatosCongreso(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('congreso');
        $oDatosCampo->setMetodoGet('getCongreso');
        $oDatosCampo->setMetodoSet('setCongreso');
        $oDatosCampo->setEtiqueta(_("congreso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(80);
        return $oDatosCampo;
    }

    private function getDatosLugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('lugar');
        $oDatosCampo->setMetodoGet('getLugar');
        $oDatosCampo->setMetodoSet('setLugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    private function getDatosF_ini(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ini');
        $oDatosCampo->setMetodoGet('getF_ini');
        $oDatosCampo->setMetodoSet('setF_ini');
        $oDatosCampo->setEtiqueta(_("fecha inicio"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosF_fin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_fin');
        $oDatosCampo->setMetodoGet('getF_fin');
        $oDatosCampo->setMetodoSet('setF_fin');
        $oDatosCampo->setEtiqueta(_("fecha fin"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosOrganiza(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('organiza');
        $oDatosCampo->setMetodoGet('getOrganiza');
        $oDatosCampo->setMetodoSet('setOrganiza');
        $oDatosCampo->setEtiqueta(_("organiza"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    private function getDatosTipo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo');
        $oDatosCampo->setMetodoGet('getTipo');
        $oDatosCampo->setMetodoSet('setTipo');
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(CongresoTipo::getArrayTiposCongreso());
        return $oDatosCampo;
    }
}