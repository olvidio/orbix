<?php

namespace src\ubis\domain\entity;

use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad u_dir_ctr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class Direccion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_direccion de Direccion
     *
     * @var int
     */
    private int $iid_direccion;
    /**
     * Direccion de Direccion
     *
     * @var string|null
     */
    private string|null $sdireccion = null;
    /**
     * C_p de Direccion
     *
     * @var string|null
     */
    private string|null $sc_p = null;
    /**
     * Poblacion de Direccion
     *
     * @var string
     */
    private string $spoblacion;
    /**
     * Provincia de Direccion
     *
     * @var string|null
     */
    private string|null $sprovincia = null;
    /**
     * A_p de Direccion
     *
     * @var string|null
     */
    private string|null $sa_p = null;
    /**
     * Pais de Direccion
     *
     * @var string|null
     */
    private string|null $spais = null;
    /**
     * F_direccion de Direccion
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_direccion = null;
    /**
     * Observ de Direccion
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Cp_dcha de Direccion
     *
     * @var bool|null
     */
    private bool|null $bcp_dcha = null;
    /**
     * Latitud de Direccion
     *
     * @var float|null
     */
    private float|null $ilatitud = null;
    /**
     * Longitud de Direccion
     *
     * @var float|null
     */
    private float|null $ilongitud = null;
    /**
     * Plano_doc de Direccion
     *
     * @var string|null
     */
    private string|null $splano_doc = null;
    /**
     * Plano_extension de Direccion
     *
     * @var string|null
     */
    private string|null $splano_extension = null;
    /**
     * Plano_nom de Direccion
     *
     * @var string|null
     */
    private string|null $splano_nom = null;
    /**
     * Nom_sede de Direccion
     *
     * @var string|null
     */
    private string|null $snom_sede = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Direccion
     */
    public function setAllAttributes(array $aDatos): Direccion
    {
        if (array_key_exists('id_direccion', $aDatos)) {
            $this->setId_direccion($aDatos['id_direccion']);
        }
        if (array_key_exists('direccion', $aDatos)) {
            $this->setDireccion($aDatos['direccion']);
        }
        if (array_key_exists('c_p', $aDatos)) {
            $this->setC_p($aDatos['c_p']);
        }
        if (array_key_exists('poblacion', $aDatos)) {
            $this->setPoblacion($aDatos['poblacion']);
        }
        if (array_key_exists('provincia', $aDatos)) {
            $this->setProvincia($aDatos['provincia']);
        }
        if (array_key_exists('a_p', $aDatos)) {
            $this->setA_p($aDatos['a_p']);
        }
        if (array_key_exists('pais', $aDatos)) {
            $this->setPais($aDatos['pais']);
        }
        if (array_key_exists('f_direccion', $aDatos)) {
            $this->setF_direccion($aDatos['f_direccion']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('cp_dcha', $aDatos)) {
            $this->setCp_dcha(is_true($aDatos['cp_dcha']));
        }
        if (array_key_exists('latitud', $aDatos)) {
            $this->setLatitud($aDatos['latitud']);
        }
        if (array_key_exists('longitud', $aDatos)) {
            $this->setLongitud($aDatos['longitud']);
        }
        if (array_key_exists('plano_doc', $aDatos)) {
            $this->setPlano_doc($aDatos['plano_doc']);
        }
        if (array_key_exists('plano_extension', $aDatos)) {
            $this->setPlano_extension($aDatos['plano_extension']);
        }
        if (array_key_exists('plano_nom', $aDatos)) {
            $this->setPlano_nom($aDatos['plano_nom']);
        }
        if (array_key_exists('nom_sede', $aDatos)) {
            $this->setNom_sede($aDatos['nom_sede']);
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
     * @return string|null $sdireccion
     */
    public function getDireccion(): ?string
    {
        return $this->sdireccion;
    }

    /**
     *
     * @param string|null $sdireccion
     */
    public function setDireccion(?string $sdireccion = null): void
    {
        $this->sdireccion = $sdireccion;
    }

    /**
     *
     * @return string|null $sc_p
     */
    public function getC_p(): ?string
    {
        return $this->sc_p;
    }

    /**
     *
     * @param string|null $sc_p
     */
    public function setC_p(?string $sc_p = null): void
    {
        $this->sc_p = $sc_p;
    }

    /**
     *
     * @return string $spoblacion
     */
    public function getPoblacion(): string
    {
        return $this->spoblacion;
    }

    /**
     *
     * @param string $spoblacion
     */
    public function setPoblacion(string $spoblacion): void
    {
        $this->spoblacion = $spoblacion;
    }

    /**
     *
     * @return string|null $sprovincia
     */
    public function getProvincia(): ?string
    {
        return $this->sprovincia;
    }

    /**
     *
     * @param string|null $sprovincia
     */
    public function setProvincia(?string $sprovincia = null): void
    {
        $this->sprovincia = $sprovincia;
    }

    /**
     *
     * @return string|null $sa_p
     */
    public function getA_p(): ?string
    {
        return $this->sa_p;
    }

    /**
     *
     * @param string|null $sa_p
     */
    public function setA_p(?string $sa_p = null): void
    {
        $this->sa_p = $sa_p;
    }

    /**
     *
     * @return string|null $spais
     */
    public function getPais(): ?string
    {
        return $this->spais;
    }

    /**
     *
     * @param string|null $spais
     */
    public function setPais(?string $spais = null): void
    {
        $this->spais = $spais;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_direccion
     */
    public function getF_direccion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_direccion ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_direccion
     */
    public function setF_direccion(DateTimeLocal|null $df_direccion = null): void
    {
        $this->df_direccion = $df_direccion;
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

    /**
     *
     * @return bool|null $bcp_dcha
     */
    public function isCp_dcha(): ?bool
    {
        return $this->bcp_dcha;
    }

    /**
     *
     * @param bool|null $bcp_dcha
     */
    public function setCp_dcha(?bool $bcp_dcha = null): void
    {
        $this->bcp_dcha = $bcp_dcha;
    }

    /**
     *
     * @return float|null $ilatitud
     */
    public function getLatitud(): ?float
    {
        return $this->ilatitud;
    }

    /**
     *
     * @param float|null $ilatitud
     */
    public function setLatitud(?float $ilatitud = null): void
    {
        $this->ilatitud = $ilatitud;
    }

    /**
     *
     * @return float|null $ilongitud
     */
    public function getLongitud(): ?float
    {
        return $this->ilongitud;
    }

    /**
     *
     * @param float|null $ilongitud
     */
    public function setLongitud(?float $ilongitud = null): void
    {
        $this->ilongitud = $ilongitud;
    }

    /**
     *
     * @return string|null $splano_doc
     */
    public function getPlano_doc(): ?string
    {
        return $this->splano_doc;
    }

    /**
     *
     * @param string|null $splano_doc
     */
    public function setPlano_doc(?string $splano_doc = null): void
    {
        $this->splano_doc = $splano_doc;
    }

    /**
     *
     * @return string|null $splano_extension
     */
    public function getPlano_extension(): ?string
    {
        return $this->splano_extension;
    }

    /**
     *
     * @param string|null $splano_extension
     */
    public function setPlano_extension(?string $splano_extension = null): void
    {
        $this->splano_extension = $splano_extension;
    }

    /**
     *
     * @return string|null $splano_nom
     */
    public function getPlano_nom(): ?string
    {
        return $this->splano_nom;
    }

    /**
     *
     * @param string|null $splano_nom
     */
    public function setPlano_nom(?string $splano_nom = null): void
    {
        $this->splano_nom = $splano_nom;
    }

    /**
     *
     * @return string|null $snom_sede
     */
    public function getNom_sede(): ?string
    {
        return $this->snom_sede;
    }

    /**
     *
     * @param string|null $snom_sede
     */
    public function setNom_sede(?string $snom_sede = null): void
    {
        $this->snom_sede = $snom_sede;
    }

    /*  -------------------------------------------------------------------------   */

    /**
     * texte amb l'adreça formatejada
     *
     */
    public function getDireccionPostal($salto_linea = '<br>', $espacio = ' ')
    {
        $txt = '';
        $rtn = $salto_linea;
        $spc = $espacio;
        if (isset($this->sdireccion)) $txt .= $this->sdireccion . $rtn;
        if (is_true($this->bcp_dcha)) {
            if (!empty($this->spoblacion)) $txt .= $this->spoblacion . $spc;
            if (!empty($this->sc_p)) $txt .= $this->sc_p;
        } else {
            if (!empty($this->sc_p)) $txt .= $this->sc_p . $spc;
            if (!empty($this->spoblacion)) $txt .= $this->spoblacion;
        }
        $txt .= $rtn;
        if (!empty($this->sa_p)) $txt .= $this->sa_p . $rtn;

        return $txt;
    }

}