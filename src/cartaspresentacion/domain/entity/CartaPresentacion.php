<?php

namespace src\cartaspresentacion\domain\entity;
/**
 * Clase que implementa la entidad du_presentacion_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class CartaPresentacion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_direccion de CartaPresentacion
     *
     * @var int
     */
    private int $iid_direccion;
    /**
     * Id_ubi de CartaPresentacion
     *
     * @var int
     */
    private int $iid_ubi;
    /**
     * Pres_nom de CartaPresentacion
     *
     * @var string|null
     */
    private string|null $spres_nom = null;
    /**
     * Pres_telf de CartaPresentacion
     *
     * @var string|null
     */
    private string|null $spres_telf = null;
    /**
     * Pres_mail de CartaPresentacion
     *
     * @var string|null
     */
    private string|null $spres_mail = null;
    /**
     * Zona de CartaPresentacion
     *
     * @var string|null
     */
    private string|null $szona = null;
    /**
     * Observ de CartaPresentacion
     *
     * @var string|null
     */
    private string|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CartaPresentacion
     */
    public function setAllAttributes(array $aDatos): CartaPresentacion
    {
        if (array_key_exists('id_direccion', $aDatos)) {
            $this->setId_direccion($aDatos['id_direccion']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('pres_nom', $aDatos)) {
            $this->setPres_nom($aDatos['pres_nom']);
        }
        if (array_key_exists('pres_telf', $aDatos)) {
            $this->setPres_telf($aDatos['pres_telf']);
        }
        if (array_key_exists('pres_mail', $aDatos)) {
            $this->setPres_mail($aDatos['pres_mail']);
        }
        if (array_key_exists('zona', $aDatos)) {
            $this->setZona($aDatos['zona']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_direccion
     */
    public function getId_direccion(): int
    {
        return $this->iid_direccion;
    }

    /**
     *
     * @param int $iid_direccion
     */
    public function setId_direccion(int $iid_direccion): void
    {
        $this->iid_direccion = $iid_direccion;
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
     * @return string|null $spres_nom
     */
    public function getPres_nom(): ?string
    {
        return $this->spres_nom;
    }

    /**
     *
     * @param string|null $spres_nom
     */
    public function setPres_nom(?string $spres_nom = null): void
    {
        $this->spres_nom = $spres_nom;
    }

    /**
     *
     * @return string|null $spres_telf
     */
    public function getPres_telf(): ?string
    {
        return $this->spres_telf;
    }

    /**
     *
     * @param string|null $spres_telf
     */
    public function setPres_telf(?string $spres_telf = null): void
    {
        $this->spres_telf = $spres_telf;
    }

    /**
     *
     * @return string|null $spres_mail
     */
    public function getPres_mail(): ?string
    {
        return $this->spres_mail;
    }

    /**
     *
     * @param string|null $spres_mail
     */
    public function setPres_mail(?string $spres_mail = null): void
    {
        $this->spres_mail = $spres_mail;
    }

    /**
     *
     * @return string|null $szona
     */
    public function getZona(): ?string
    {
        return $this->szona;
    }

    /**
     *
     * @param string|null $szona
     */
    public function setZona(?string $szona = null): void
    {
        $this->szona = $szona;
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }
}