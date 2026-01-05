<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CongresoName;
use src\profesores\domain\value_objects\FechaFin;
use src\profesores\domain\value_objects\FechaInicio;
use src\profesores\domain\value_objects\LugarName;
use src\profesores\domain\value_objects\OrganizaName;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;


class ProfesorCongreso
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private string $congreso;

    private string|null $lugar = null;

    private DateTimeLocal|null $f_ini = null;

    private DateTimeLocal|null $f_fin = null;

    private string|null $organiza = null;

    private int|null $tipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
    public static function getTiposCongreso(): array
    {
        $tipos_congreso = [
            1 => _("cv"),
            2 => _("congreso"),
            3 => _("reunión"),
            4 => _("claustro"),
        ];

        return $tipos_congreso;
    }

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated Usar getCongresoVo()->value()
     */
    public function getCongreso(): string
    {
        return $this->congreso;
    }

    /**
     * @deprecated Usar setCongresoVo(CongresoName $vo)
     */
    public function setCongreso(string $congreso): void
    {
        $this->congreso = $congreso;
    }

    public function getCongresoVo(): CongresoName
    {
        return new CongresoName($this->congreso);
    }

    public function setCongresoVo(?CongresoName $congreso): void
    {
        if ($congreso !== null) {
            $this->congreso = $congreso->value();
        }
    }

    /**
     * @deprecated Usar getLugarVo()->value()
     */
    public function getLugar(): ?string
    {
        return $this->lugar;
    }

    /**
     * @deprecated Usar setLugarVo(LugarName $vo)
     */
    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = $lugar;
    }

    public function getLugarVo(): ?LugarName
    {
        return LugarName::fromNullable($this->lugar);
    }

    public function setLugarVo(?LugarName $lugar): void
    {
        $this->lugar = $lugar?->value();
    }

    /**
     * @deprecated Usar getFIniVo()->value()
     */
    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ini ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFIniVo(FechaInicio $vo)
     */
    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }

    public function getFIniVo(): ?FechaInicio
    {
        return FechaInicio::fromNullable($this->f_ini);
    }

    public function setFIniVo(?FechaInicio $fini): void
    {
        $this->f_ini = $fini?->value();
    }

    /**
     * @deprecated Usar getFFinVo()->value()
     */
    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFFinVo(FechaFin $vo)
     */
    public function setF_fin(DateTimeLocal|null $f_fin = null): void
    {
        $this->f_fin = $f_fin;
    }

    public function getFFinVo(): ?FechaFin
    {
        return FechaFin::fromNullable($this->f_fin);
    }

    public function setFFinVo(?FechaFin $ffin): void
    {
        $this->f_fin = $ffin?->value();
    }

    /**
     * @deprecated Usar getOrganizaVo()->value()
     */
    public function getOrganiza(): ?string
    {
        return $this->organiza;
    }

    /**
     * @deprecated Usar setOrganizaVo(OrganizaName $vo)
     */
    public function setOrganiza(?string $organiza = null): void
    {
        $this->organiza = $organiza;
    }

    public function getOrganizaVo(): ?OrganizaName
    {
        return OrganizaName::fromNullable($this->organiza);
    }

    public function setOrganizaVo(?OrganizaName $organiza): void
    {
        $this->organiza = $organiza?->value();
    }


    public function getTipo(): ?int
    {
        return $this->tipo;
    }


    public function setTipo(?int $tipo = null): void
    {
        $this->tipo = $tipo;
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
        $oDatosCampo->setLista(self::getTiposCongreso());
        return $oDatosCampo;
    }
}