<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\CongresoName;
use src\profesores\domain\value_objects\FechaFin;
use src\profesores\domain\value_objects\FechaInicio;
use src\profesores\domain\value_objects\LugarName;
use src\profesores\domain\value_objects\OrganizaName;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad d_congresos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorCongreso
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorCongreso
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorCongreso
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Congreso de ProfesorCongreso
     *
     * @var string
     */
    private string $scongreso;
    /**
     * Lugar de ProfesorCongreso
     *
     * @var string|null
     */
    private string|null $slugar = null;
    /**
     * F_ini de ProfesorCongreso
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_ini = null;
    /**
     * F_fin de ProfesorCongreso
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_fin = null;
    /**
     * Organiza de ProfesorCongreso
     *
     * @var string|null
     */
    private string|null $sorganiza = null;
    /**
     * Tipo de ProfesorCongreso
     *
     * @var int|null
     */
    private int|null $itipo = null;

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

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorCongreso
     */
    public function setAllAttributes(array $aDatos): ProfesorCongreso
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('congreso', $aDatos)) {
            $this->setCongresoVo(CongresoName::fromNullable($aDatos['congreso']));
        }
        if (array_key_exists('lugar', $aDatos)) {
            $this->setLugarVo(LugarName::fromNullable($aDatos['lugar']));
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setFIniVo(FechaInicio::fromNullable($aDatos['f_ini']));
        }
        if (array_key_exists('f_fin', $aDatos)) {
            $this->setFFinVo(FechaFin::fromNullable($aDatos['f_fin']));
        }
        if (array_key_exists('organiza', $aDatos)) {
            $this->setOrganizaVo(OrganizaName::fromNullable($aDatos['organiza']));
        }
        if (array_key_exists('tipo', $aDatos)) {
            $this->setTipo($aDatos['tipo']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * @return string $scongreso
     * @deprecated Usar getCongresoVo()->value()
     */
    public function getCongreso(): string
    {
        return $this->scongreso;
    }

    /**
     * @param string $scongreso
     * @deprecated Usar setCongresoVo(CongresoName $vo)
     */
    public function setCongreso(string $scongreso): void
    {
        $this->scongreso = $scongreso;
    }

    public function getCongresoVo(): CongresoName
    {
        return new CongresoName($this->scongreso);
    }

    public function setCongresoVo(?CongresoName $congreso): void
    {
        if ($congreso !== null) {
            $this->scongreso = $congreso->value();
        }
    }

    /**
     * @return string|null $slugar
     * @deprecated Usar getLugarVo()->value()
     */
    public function getLugar(): ?string
    {
        return $this->slugar;
    }

    /**
     * @param string|null $slugar
     * @deprecated Usar setLugarVo(LugarName $vo)
     */
    public function setLugar(?string $slugar = null): void
    {
        $this->slugar = $slugar;
    }

    public function getLugarVo(): ?LugarName
    {
        return LugarName::fromNullable($this->slugar);
    }

    public function setLugarVo(?LugarName $lugar): void
    {
        $this->slugar = $lugar?->value();
    }

    /**
     * @return DateTimeLocal|NullDateTimeLocal|null $df_ini
     * @deprecated Usar getFIniVo()->value()
     */
    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_ini ?? new NullDateTimeLocal;
    }

    /**
     * @param DateTimeLocal|null $df_ini
     * @deprecated Usar setFIniVo(FechaInicio $vo)
     */
    public function setF_ini(DateTimeLocal|null $df_ini = null): void
    {
        $this->df_ini = $df_ini;
    }

    public function getFIniVo(): ?FechaInicio
    {
        return FechaInicio::fromNullable($this->df_ini);
    }

    public function setFIniVo(?FechaInicio $fini): void
    {
        $this->df_ini = $fini?->value();
    }

    /**
     * @return DateTimeLocal|NullDateTimeLocal|null $df_fin
     * @deprecated Usar getFFinVo()->value()
     */
    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_fin ?? new NullDateTimeLocal;
    }

    /**
     * @param DateTimeLocal|null $df_fin
     * @deprecated Usar setFFinVo(FechaFin $vo)
     */
    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->df_fin = $df_fin;
    }

    public function getFFinVo(): ?FechaFin
    {
        return FechaFin::fromNullable($this->df_fin);
    }

    public function setFFinVo(?FechaFin $ffin): void
    {
        $this->df_fin = $ffin?->value();
    }

    /**
     * @return string|null $sorganiza
     * @deprecated Usar getOrganizaVo()->value()
     */
    public function getOrganiza(): ?string
    {
        return $this->sorganiza;
    }

    /**
     * @param string|null $sorganiza
     * @deprecated Usar setOrganizaVo(OrganizaName $vo)
     */
    public function setOrganiza(?string $sorganiza = null): void
    {
        $this->sorganiza = $sorganiza;
    }

    public function getOrganizaVo(): ?OrganizaName
    {
        return OrganizaName::fromNullable($this->sorganiza);
    }

    public function setOrganizaVo(?OrganizaName $organiza): void
    {
        $this->sorganiza = $organiza?->value();
    }

    /**
     *
     * @return int|null $itipo
     */
    public function getTipo(): ?int
    {
        return $this->itipo;
    }

    /**
     *
     * @param int|null $itipo
     */
    public function setTipo(?int $itipo = null): void
    {
        $this->itipo = $itipo;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
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

    function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    function getDatosCongreso(): DatosCampo
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

    function getDatosLugar(): DatosCampo
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

    function getDatosF_ini(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ini');
        $oDatosCampo->setMetodoGet('getF_ini');
        $oDatosCampo->setMetodoSet('setF_ini');
        $oDatosCampo->setEtiqueta(_("fecha inicio"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosF_fin(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_fin');
        $oDatosCampo->setMetodoGet('getF_fin');
        $oDatosCampo->setMetodoSet('setF_fin');
        $oDatosCampo->setEtiqueta(_("fecha fin"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosOrganiza(): DatosCampo
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

    function getDatosTipo(): DatosCampo
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