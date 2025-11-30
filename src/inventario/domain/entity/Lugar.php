<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\value_objects\LugarName;

/**
 * Clase que implementa la entidad i_lugares_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Lugar
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_lugar de Lugar
     *
     * @var int
     */
    private int $iid_lugar;
    /**
     * Id_ubi de Lugar
     *
     * @var int
     */
    private int $iid_ubi;
    /**
     * Nom_lugar de Lugar
     *
     * @var string
     */
    private string $snom_lugar;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Lugar
     */
    public function setAllAttributes(array $aDatos): Lugar
    {
        if (array_key_exists('id_lugar', $aDatos)) {
            $this->setId_lugar($aDatos['id_lugar']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('nom_lugar', $aDatos)) {
            $this->setNom_lugar($aDatos['nom_lugar']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_lugar
     */
    public function getId_lugar(): int
    {
        return $this->iid_lugar;
    }

    /**
     *
     * @param int $iid_lugar
     */
    public function setId_lugar(int $iid_lugar): void
    {
        $this->iid_lugar = $iid_lugar;
    }

    /**
     *
     * @return int $iid_ubi
     */
    public function getId_ubi(): int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int $iid_ubi
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return string $snom_lugar
     */
    public function getNom_lugar(): string
    {
        return $this->snom_lugar;
    }

    /**
     *
     * @param string $snom_lugar
     */
    public function setNom_lugar(string $snom_lugar): void
    {
        $this->snom_lugar = $snom_lugar;
    }

    // Value Object API (duplicada con legacy)
    public function getNomLugarVo(): LugarName
    {
        return new LugarName($this->snom_lugar);
    }

    public function setNomLugarVo(LugarName $name): void
    {
        $this->snom_lugar = $name->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_lugar';
    }

    public function getDatosCampos():array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosId_ubi());
        $oSet->add($this->getDatosNom_lugar());
        return $oSet->getTot();
    }

    private function getDatosId_ubi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi');
        $oDatosCampo->setMetodoGet('getId_ubi');
        $oDatosCampo->setMetodoSet('setId_ubi');
        $oDatosCampo->setEtiqueta(_("centro/casa"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(UbiInventarioRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom_ubi');
        $oDatosCampo->setArgument3('getArrayUbisInventario');
        return $oDatosCampo;
    }

    private function getDatosNom_lugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_lugar');
        $oDatosCampo->setMetodoGet('getNom_lugar');
        $oDatosCampo->setMetodoSet('setNom_lugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

}