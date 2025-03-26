<?php

namespace inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad i_equipajes_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Equipaje
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_equipaje de Equipaje
     *
     * @var int
     */
    private int $iid_equipaje;
    /**
     * Ids_activ de Equipaje
     *
     * @var string|null
     */
    private string|null $sids_activ = null;
    /**
     * Lugar de Equipaje
     *
     * @var string|null
     */
    private string|null $slugar = null;
    /**
     * F_ini de Equipaje
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_ini = null;
    /**
     * F_fin de Equipaje
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_fin = null;
    /**
     * Id_ubi_activ de Equipaje
     *
     * @var int|null
     */
    private int|null $iid_ubi_activ = null;
    /**
     * Nom_equipaje de Equipaje
     *
     * @var string|null
     */
    private string|null $snom_equipaje = null;
    /**
     * Cabecera de Equipaje
     *
     * @var string|null
     */
    private string|null $scabecera = null;
    /**
     * Pie de Equipaje
     *
     * @var string|null
     */
    private string|null $spie = null;
    /**
     * Cabecerab de Equipaje
     *
     * @var string|null
     */
    private string|null $scabecerab = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Equipaje
     */
    public function setAllAttributes(array $aDatos): Equipaje
    {
        if (array_key_exists('id_equipaje', $aDatos)) {
            $this->setId_equipaje($aDatos['id_equipaje']);
        }
        if (array_key_exists('ids_activ', $aDatos)) {
            $this->setIds_activ($aDatos['ids_activ']);
        }
        if (array_key_exists('lugar', $aDatos)) {
            $this->setLugar($aDatos['lugar']);
        }
        if (array_key_exists('f_ini', $aDatos)) {
            $this->setF_ini($aDatos['f_ini']);
        }
        if (array_key_exists('f_fin', $aDatos)) {
            $this->setF_fin($aDatos['f_fin']);
        }
        if (array_key_exists('id_ubi_activ', $aDatos)) {
            $this->setId_ubi_activ($aDatos['id_ubi_activ']);
        }
        if (array_key_exists('nom_equipaje', $aDatos)) {
            $this->setNom_equipaje($aDatos['nom_equipaje']);
        }
        if (array_key_exists('cabecera', $aDatos)) {
            $this->setCabecera($aDatos['cabecera']);
        }
        if (array_key_exists('pie', $aDatos)) {
            $this->setPie($aDatos['pie']);
        }
        if (array_key_exists('cabecerab', $aDatos)) {
            $this->setCabecerab($aDatos['cabecerab']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_equipaje
     */
    public function getId_equipaje(): int
    {
        return $this->iid_equipaje;
    }

    /**
     *
     * @param int $iid_equipaje
     */
    public function setId_equipaje(int $iid_equipaje): void
    {
        $this->iid_equipaje = $iid_equipaje;
    }

    /**
     *
     * @return string|null $sids_activ
     */
    public function getIds_activ(): ?string
    {
        return $this->sids_activ;
    }

    /**
     *
     * @param string|null $sids_activ
     */
    public function setIds_activ(?string $sids_activ = null): void
    {
        $this->sids_activ = $sids_activ;
    }

    /**
     *
     * @return string|null $slugar
     */
    public function getLugar(): ?string
    {
        return $this->slugar;
    }

    /**
     *
     * @param string|null $slugar
     */
    public function setLugar(?string $slugar = null): void
    {
        $this->slugar = $slugar;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_ini
     */
    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_ini ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_ini
     */
    public function setF_ini(DateTimeLocal|null $df_ini = null): void
    {
        $this->df_ini = $df_ini;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_fin
     */
    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_fin ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_fin
     */
    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->df_fin = $df_fin;
    }

    /**
     *
     * @return int|null $iid_ubi_activ
     */
    public function getId_ubi_activ(): ?int
    {
        return $this->iid_ubi_activ;
    }

    /**
     *
     * @param int|null $iid_ubi_activ
     */
    public function setId_ubi_activ(?int $iid_ubi_activ = null): void
    {
        $this->iid_ubi_activ = $iid_ubi_activ;
    }

    /**
     *
     * @return string|null $snom_equipaje
     */
    public function getNom_equipaje(): ?string
    {
        return $this->snom_equipaje;
    }

    /**
     *
     * @param string|null $snom_equipaje
     */
    public function setNom_equipaje(?string $snom_equipaje = null): void
    {
        $this->snom_equipaje = $snom_equipaje;
    }

    /**
     *
     * @return string|null $scabecera
     */
    public function getCabecera(): ?string
    {
        return $this->scabecera;
    }

    /**
     *
     * @param string|null $scabecera
     */
    public function setCabecera(?string $scabecera = null): void
    {
        $this->scabecera = $scabecera;
    }

    /**
     *
     * @return string|null $spie
     */
    public function getPie(): ?string
    {
        return $this->spie;
    }

    /**
     *
     * @param string|null $spie
     */
    public function setPie(?string $spie = null): void
    {
        $this->spie = $spie;
    }

    /**
     *
     * @return string|null $scabecerab
     */
    public function getCabecerab(): ?string
    {
        return $this->scabecerab;
    }

    /**
     *
     * @param string|null $scabecerab
     */
    public function setCabecerab(?string $scabecerab = null): void
    {
        $this->scabecerab = $scabecerab;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_equipaje';
    }

    function getDatosCampos()
    {
        $oEquipajeSet = new Set();

        $oEquipajeSet->add($this->getDatosIds_activ());
        $oEquipajeSet->add($this->getDatosLugar());
        $oEquipajeSet->add($this->getDatosF_ini());
        $oEquipajeSet->add($this->getDatosF_fin());
        $oEquipajeSet->add($this->getDatosId_ubi_activ());
        $oEquipajeSet->add($this->getDatosNom_equipaje());
        $oEquipajeSet->add($this->getDatosCabecera());
        $oEquipajeSet->add($this->getDatosCabeceraB());
        $oEquipajeSet->add($this->getDatosPie());
        return $oEquipajeSet->getTot();
    }

    function getDatosIds_activ()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ids_activ');
        $oDatosCampo->setMetodoGet('getIds_activ');
        $oDatosCampo->setMetodoSet('setIds_activ');
        $oDatosCampo->setEtiqueta(_("ids_activ"));
        return $oDatosCampo;
    }

    function getDatosLugar()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('lugar');
        $oDatosCampo->setMetodoGet('getLugar');
        $oDatosCampo->setMetodoSet('setLugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        return $oDatosCampo;
    }

    function getDatosF_ini()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ini');
        $oDatosCampo->setMetodoGet('getF_ini');
        $oDatosCampo->setMetodoSet('setF_ini');
        $oDatosCampo->setEtiqueta(_("f_ini"));
        return $oDatosCampo;
    }

    function getDatosF_fin()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_fin');
        $oDatosCampo->setMetodoGet('getF_fin');
        $oDatosCampo->setMetodoSet('setF_fin');
        $oDatosCampo->setEtiqueta(_("f_fin"));
        return $oDatosCampo;
    }

    function getDatosId_ubi_activ()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi_activ');
        $oDatosCampo->setMetodoGet('getId_ubi_activ');
        $oDatosCampo->setMetodoSet('setId_ubi_activ');
        $oDatosCampo->setEtiqueta(_("id_ubi_activ"));
        return $oDatosCampo;
    }

    function getDatosNom_equipaje()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_equipaje');
        $oDatosCampo->setMetodoGet('getNom_equipaje');
        $oDatosCampo->setMetodoSet('setNom_equipaje');
        $oDatosCampo->setEtiqueta(_("nom_equipaje"));
        return $oDatosCampo;
    }

    function getDatosCabecera()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cabecera');
        $oDatosCampo->setMetodoGet('getCabecera');
        $oDatosCampo->setMetodoSet('setCabecera');
        $oDatosCampo->setEtiqueta(_("cabecera"));
        return $oDatosCampo;
    }

    function getDatosCabeceraB()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cabecerab');
        $oDatosCampo->setMetodoGet('getCabecerab');
        $oDatosCampo->setMetodoSet('setCabecerab');
        $oDatosCampo->setEtiqueta(_("cabecera B"));
        return $oDatosCampo;
    }

    function getDatosPie()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('pie');
        $oDatosCampo->setMetodoGet('getPie');
        $oDatosCampo->setMetodoSet('setPie');
        $oDatosCampo->setEtiqueta(_("pie"));
        return $oDatosCampo;
    }

}