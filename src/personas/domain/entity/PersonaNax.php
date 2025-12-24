<?php

namespace src\personas\domain\entity;

use src\personas\domain\value_objects\{CeLugarText, CeNumber};
use function core\is_true;

/**
 * Clase que implementa la entidad p_numerarios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 9/12/2025
 */
class PersonaNax extends PersonaDl
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_auto de PersonaNax
     *
     * @var int
     */
    private int $iid_auto;
    /**
     * Ce de PersonaNax
     *
     * @var int|null
     */
    private int|null $ice = null;
    /**
     * Ce_ini de PersonaNax
     *
     * @var int|null
     */
    private int|null $ice_ini = null;
    /**
     * Ce_fin de PersonaNax
     *
     * @var int|null
     */
    private int|null $ice_fin = null;
    /**
     * Ce_lugar de PersonaNax
     *
     * @var string|null
     */
    private string|null $sce_lugar = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return PersonaNax
     */
    public function setAllAttributes(array $aDatos): PersonaNax
    {
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('id_tabla', $aDatos)) {
            $this->setId_tabla($aDatos['id_tabla']);
        }
        if (array_key_exists('dl', $aDatos)) {
            $this->setDl($aDatos['dl']);
        }
        if (array_key_exists('sacd', $aDatos)) {
            $this->setSacd(is_true($aDatos['sacd']));
        }
        if (array_key_exists('trato', $aDatos)) {
            $this->setTrato($aDatos['trato']);
        }
        if (array_key_exists('nom', $aDatos)) {
            $this->setNom($aDatos['nom']);
        }
        if (array_key_exists('nx1', $aDatos)) {
            $this->setNx1($aDatos['nx1']);
        }
        if (array_key_exists('apellido1', $aDatos)) {
            $this->setApellido1($aDatos['apellido1']);
        }
        if (array_key_exists('nx2', $aDatos)) {
            $this->setNx2($aDatos['nx2']);
        }
        if (array_key_exists('apellido2', $aDatos)) {
            $this->setApellido2($aDatos['apellido2']);
        }
        if (array_key_exists('f_nacimiento', $aDatos)) {
            $this->setF_nacimiento($aDatos['f_nacimiento']);
        }
        if (array_key_exists('idioma_preferido', $aDatos)) {
            $this->setIdioma_preferido($aDatos['idioma_preferido']);
        }
        if (array_key_exists('situacion', $aDatos)) {
            $this->setSituacion($aDatos['situacion']);
        }
        if (array_key_exists('f_situacion', $aDatos)) {
            $this->setF_situacion($aDatos['f_situacion']);
        }
        if (array_key_exists('apel_fam', $aDatos)) {
            $this->setApel_fam($aDatos['apel_fam']);
        }
        if (array_key_exists('inc', $aDatos)) {
            $this->setInc($aDatos['inc']);
        }
        if (array_key_exists('f_inc', $aDatos)) {
            $this->setF_inc($aDatos['f_inc']);
        }
        if (array_key_exists('nivel_stgr', $aDatos)) {
            $this->setNivel_stgr($aDatos['nivel_stgr']);
        }
        if (array_key_exists('profesion', $aDatos)) {
            $this->setProfesion($aDatos['profesion']);
        }
        if (array_key_exists('eap', $aDatos)) {
            $this->setEap($aDatos['eap']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('id_ctr', $aDatos)) {
            $this->setId_ctr($aDatos['id_ctr']);
        }
        if (array_key_exists('lugar_nacimiento', $aDatos)) {
            $this->setLugar_nacimiento($aDatos['lugar_nacimiento']);
        }
        if (array_key_exists('id_auto', $aDatos)) {
            $this->setId_auto($aDatos['id_auto']);
        }
        if (array_key_exists('ce', $aDatos)) {
            $this->setCe($aDatos['ce']);
        }
        if (array_key_exists('ce_ini', $aDatos)) {
            $this->setCe_ini($aDatos['ce_ini']);
        }
        if (array_key_exists('ce_fin', $aDatos)) {
            $this->setCe_fin($aDatos['ce_fin']);
        }
        if (array_key_exists('ce_lugar', $aDatos)) {
            $this->setCe_lugar($aDatos['ce_lugar']);
        }
        return $this;
    }


    /**
     *
     * @return int $iid_auto
     */
    public function getId_auto(): int
    {
        return $this->iid_auto;
    }

    /**
     *
     * @param int $iid_auto
     */
    public function setId_auto(int $iid_auto): void
    {
        $this->iid_auto = $iid_auto;
    }

    /**
     *
     * @return int|null $ice
     */
    /**
     * @deprecated use getCeVo() instead
     */
    public function getCe(): ?int
    {
        return $this->ice;
    }

    /**
     *
     * @param int|null $ice
     */
    /**
     * @deprecated use setCeVo() instead
     */
    public function setCe(?int $ice = null): void
    {
        $this->ice = $ice;
    }

    public function getCeVo(): ?CeNumber
    {
        return CeNumber::fromNullableInt($this->ice ?? null);
    }

    public function setCeVo(?CeNumber $ce = null): void
    {
        $this->ice = $ce?->value();
    }

    /**
     *
     * @return int|null $ice_ini
     */
    /**
     * @deprecated use getCeIniVo() instead
     */
    public function getCe_ini(): ?int
    {
        return $this->ice_ini;
    }

    /**
     *
     * @param int|null $ice_ini
     */
    /**
     * @deprecated use setCeIniVo() instead
     */
    public function setCe_ini(?int $ice_ini = null): void
    {
        $this->ice_ini = $ice_ini;
    }

    public function getCeIniVo(): ?CeNumber
    {
        return CeNumber::fromNullableInt($this->ice_ini ?? null);
    }

    public function setCeIniVo(?CeNumber $ce = null): void
    {
        $this->ice_ini = $ce?->value();
    }

    /**
     *
     * @return int|null $ice_fin
     */
    /**
     * @deprecated use getCeFinVo() instead
     */
    public function getCe_fin(): ?int
    {
        return $this->ice_fin;
    }

    /**
     *
     * @param int|null $ice_fin
     */
    /**
     * @deprecated use setCeFinVo() instead
     */
    public function setCe_fin(?int $ice_fin = null): void
    {
        $this->ice_fin = $ice_fin;
    }

    public function getCeFinVo(): ?CeNumber
    {
        return CeNumber::fromNullableInt($this->ice_fin ?? null);
    }

    public function setCeFinVo(?CeNumber $ce = null): void
    {
        $this->ice_fin = $ce?->value();
    }

    /**
     *
     * @return string|null $sce_lugar
     */
    /**
     * @deprecated use getCeLugarVo() instead
     */
    public function getCe_lugar(): ?string
    {
        return $this->sce_lugar;
    }

    /**
     *
     * @param string|null $sce_lugar
     */
    /**
     * @deprecated use setCeLugarVo() instead
     */
    public function setCe_lugar(?string $sce_lugar = null): void
    {
        $this->sce_lugar = $sce_lugar;
    }

    public function getCeLugarVo(): ?CeLugarText
    {
        return CeLugarText::fromNullableString($this->sce_lugar ?? null);
    }

    public function setCeLugarVo(?CeLugarText $lugar = null): void
    {
        $this->sce_lugar = $lugar?->value();
    }
}