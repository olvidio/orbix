<?php

namespace src\ubis\domain\entity;

use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;
use src\ubis\domain\value_objects\{APText,
    CodigoPostalText,
    DireccionText,
    LatitudDecimal,
    LongitudDecimal,
    SedeNameText,
    ObservDireccionText,
    PaisName,
    PlanoDocText,
    PlanoExtensionText,
    PlanoNameText,
    PoblacionText,
    ProvinciaText};

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
            $valor = $aDatos['id_direccion'];
            if ($valor instanceof \src\ubis\domain\value_objects\DireccionId) {
                $this->setIdDireccionVo($valor);
            } else {
                $this->setId_direccion((int)$valor);
            }
        }
        if (array_key_exists('direccion', $aDatos)) {
            $valor = $aDatos['direccion'];
            if ($valor instanceof DireccionText || $valor === null) {
                $this->setDireccionVo($valor);
            } else {
                $this->setDireccion($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('c_p', $aDatos)) {
            $valor = $aDatos['c_p'];
            if ($valor instanceof CodigoPostalText || $valor === null) {
                $this->setCodigoPostalVo($valor);
            } else {
                $this->setC_p($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('poblacion', $aDatos)) {
            $valor = $aDatos['poblacion'];
            if ($valor instanceof PoblacionText) {
                $this->setPoblacionVo($valor);
            } else {
                $this->setPoblacion((string)$valor);
            }
        }
        if (array_key_exists('provincia', $aDatos)) {
            $valor = $aDatos['provincia'];
            if ($valor instanceof ProvinciaText || $valor === null) {
                $this->setProvinciaVo($valor);
            } else {
                $this->setProvincia($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('a_p', $aDatos)) {
            $valor = $aDatos['a_p'];
            if ($valor instanceof APText || $valor === null) {
                $this->setAPVo($valor);
            } else {
                $this->setA_p($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('pais', $aDatos)) {
            $valor = $aDatos['pais'];
            if ($valor instanceof PaisName || $valor === null) {
                $this->setPaisVo($valor);
            } else {
                $this->setPais($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('f_direccion', $aDatos)) {
            $this->setF_direccion($aDatos['f_direccion']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $valor = $aDatos['observ'];
            if ($valor instanceof ObservDireccionText || $valor === null) {
                $this->setObservVo($valor);
            } else {
                $this->setObserv($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('cp_dcha', $aDatos)) {
            $this->setCp_dcha(is_true($aDatos['cp_dcha']));
        }
        if (array_key_exists('latitud', $aDatos)) {
            $valor = $aDatos['latitud'];
            if ($valor instanceof LatitudDecimal || $valor === null) {
                $this->setLatitudVo($valor);
            } else {
                $this->setLatitud($valor !== null ? (float)$valor : null);
            }
        }
        if (array_key_exists('longitud', $aDatos)) {
            $valor = $aDatos['longitud'];
            if ($valor instanceof LongitudDecimal || $valor === null) {
                $this->setLongitudVo($valor);
            } else {
                $this->setLongitud($valor !== null ? (float)$valor : null);
            }
        }
        if (array_key_exists('plano_doc', $aDatos)) {
            $valor = $aDatos['plano_doc'];
            if ($valor instanceof PlanoDocText || $valor === null) {
                $this->setPlanoDocVo($valor);
            } else {
                $this->setPlano_doc($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('plano_extension', $aDatos)) {
            $valor = $aDatos['plano_extension'];
            if ($valor instanceof PlanoExtensionText || $valor === null) {
                $this->setPlanoExtensionVo($valor);
            } else {
                $this->setPlano_extension($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('plano_nom', $aDatos)) {
            $valor = $aDatos['plano_nom'];
            if ($valor instanceof PlanoNameText || $valor === null) {
                $this->setPlanoNomVo($valor);
            } else {
                $this->setPlano_nom($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('nom_sede', $aDatos)) {
            $valor = $aDatos['nom_sede'];
            if ($valor instanceof SedeNameText || $valor === null) {
                $this->setNomSedeVo($valor);
            } else {
                $this->setNom_sede($valor !== null ? (string)$valor : null);
            }
        }
        return $this;
    }

    /**
     *
     * @return int $iid_direccion
     */
    /**
     * @deprecated Usar `getIdDireccionVo(): ?\\src\\ubis\\domain\\value_objects\\DireccionId` en su lugar.
     */
    public function getId_direccion(): int
    {
        return $this->iid_direccion;
    }

    /**
     *
     * @param int $iid_direccion
     */
    /**
     * @deprecated Usar `setIdDireccionVo(?\\src\\ubis\\domain\\value_objects\\DireccionId $id): void` en su lugar.
     */
    public function setId_direccion(int $iid_direccion): void
    {
        $this->iid_direccion = $iid_direccion;
    }

    // VO API for id_direccion
    public function getIdDireccionVo(): ?\src\ubis\domain\value_objects\DireccionId
    {
        return isset($this->iid_direccion) && $this->iid_direccion > 0 ? new \src\ubis\domain\value_objects\DireccionId($this->iid_direccion) : null;
    }

    public function setIdDireccionVo(?\src\ubis\domain\value_objects\DireccionId $id = null): void
    {
        $this->iid_direccion = $id?->value() ?? 0;
    }

    /**
     *
     * @return string|null $sdireccion
     */
    /**
     * @deprecated Usar `getDireccionVo(): ?DireccionText` en su lugar.
     */
    public function getDireccion(): ?string
    {
        return $this->sdireccion;
    }

    /**
     *
     * @param string|null $sdireccion
     */
    /**
     * @deprecated Usar `setDireccionVo(?DireccionText $direccion): void` en su lugar.
     */
    public function setDireccion(?string $sdireccion = null): void
    {
        $this->sdireccion = $sdireccion;
    }

    public function getDireccionVo(): ?DireccionText
    {
        return DireccionText::fromNullableString($this->sdireccion);
    }

    public function setDireccionVo(?DireccionText $direccion = null): void
    {
        $this->sdireccion = $direccion?->value();
    }

    /**
     *
     * @return string|null $sc_p
     */
    /**
     * @deprecated Usar `getCodigoPostalVo(): ?CodigoPostalText` en su lugar.
     */
    public function getC_p(): ?string
    {
        return $this->sc_p;
    }

    /**
     *
     * @param string|null $sc_p
     */
    /**
     * @deprecated Usar `setCodigoPostalVo(?CodigoPostalText $cp): void` en su lugar.
     */
    public function setC_p(?string $sc_p = null): void
    {
        $this->sc_p = $sc_p;
    }

    public function getCodigoPostalVo(): ?CodigoPostalText
    {
        return CodigoPostalText::fromNullableString($this->sc_p);
    }

    public function setCodigoPostalVo(?CodigoPostalText $cp = null): void
    {
        $this->sc_p = $cp?->value();
    }

    /**
     *
     * @return string $spoblacion
     */
    /**
     * @deprecated Usar `getPoblacionVo(): PoblacionText` en su lugar.
     */
    public function getPoblacion(): string
    {
        return $this->spoblacion;
    }

    /**
     *
     * @param string $spoblacion
     */
    /**
     * @deprecated Usar `setPoblacionVo(PoblacionText $poblacion): void` en su lugar.
     */
    public function setPoblacion(string $spoblacion): void
    {
        $this->spoblacion = $spoblacion;
    }

    public function getPoblacionVo(): PoblacionText
    {
        return new PoblacionText($this->spoblacion);
    }

    public function setPoblacionVo(PoblacionText $poblacion): void
    {
        $this->spoblacion = $poblacion->value();
    }

    /**
     *
     * @return string|null $sprovincia
     */
    /**
     * @deprecated Usar `getProvinciaVo(): ?ProvinciaText` en su lugar.
     */
    public function getProvincia(): ?string
    {
        return $this->sprovincia;
    }

    /**
     *
     * @param string|null $sprovincia
     */
    /**
     * @deprecated Usar `setProvinciaVo(?ProvinciaText $provincia): void` en su lugar.
     */
    public function setProvincia(?string $sprovincia = null): void
    {
        $this->sprovincia = $sprovincia;
    }

    public function getProvinciaVo(): ?ProvinciaText
    {
        return ProvinciaText::fromNullableString($this->sprovincia);
    }

    public function setProvinciaVo(?ProvinciaText $provincia = null): void
    {
        $this->sprovincia = $provincia?->value();
    }

    /**
     *
     * @return string|null $sa_p
     */
    /**
     * @deprecated Usar `getAPVo(): ?APText` en su lugar.
     */
    public function getA_p(): ?string
    {
        return $this->sa_p;
    }

    /**
     *
     * @param string|null $sa_p
     */
    /**
     * @deprecated Usar `setAPVo(?APText $ap): void` en su lugar.
     */
    public function setA_p(?string $sa_p = null): void
    {
        $this->sa_p = $sa_p;
    }

    public function getAPVo(): ?APText
    {
        return APText::fromNullableString($this->sa_p);
    }

    public function setAPVo(?APText $ap = null): void
    {
        $this->sa_p = $ap?->value();
    }

    /**
     *
     * @return string|null $spais
     */
    /**
     * @deprecated Usar `getPaisVo(): ?PaisName` en su lugar.
     */
    public function getPais(): ?string
    {
        return $this->spais;
    }

    /**
     *
     * @param string|null $spais
     */
    /**
     * @deprecated Usar `setPaisVo(?PaisName $pais): void` en su lugar.
     */
    public function setPais(?string $spais = null): void
    {
        $this->spais = $spais;
    }

    public function getPaisVo(): ?PaisName
    {
        return PaisName::fromNullableString($this->spais);
    }

    public function setPaisVo(?PaisName $pais = null): void
    {
        $this->spais = $pais?->value();
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
    /**
     * @deprecated Usar `getObservVo(): ?ObservDireccionText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    /**
     * @deprecated Usar `setObservVo(?ObservDireccionText $observ): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    public function getObservVo(): ?ObservDireccionText
    {
        return ObservDireccionText::fromNullableString($this->sobserv);
    }

    public function setObservVo(?ObservDireccionText $observ = null): void
    {
        $this->sobserv = $observ?->value();
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
    /**
     * @deprecated Usar `getLatitudVo(): ?LatitudDecimal` en su lugar.
     */
    public function getLatitud(): ?float
    {
        return $this->ilatitud;
    }

    /**
     *
     * @param float|null $ilatitud
     */
    /**
     * @deprecated Usar `setLatitudVo(?LatitudDecimal $lat): void` en su lugar.
     */
    public function setLatitud(?float $ilatitud = null): void
    {
        $this->ilatitud = $ilatitud;
    }

    public function getLatitudVo(): ?LatitudDecimal
    {
        return LatitudDecimal::fromNullableFloat($this->ilatitud);
    }

    public function setLatitudVo(?LatitudDecimal $lat = null): void
    {
        $this->ilatitud = $lat?->value();
    }

    /**
     *
     * @return float|null $ilongitud
     */
    /**
     * @deprecated Usar `getLongitudVo(): ?LongitudDecimal` en su lugar.
     */
    public function getLongitud(): ?float
    {
        return $this->ilongitud;
    }

    /**
     *
     * @param float|null $ilongitud
     */
    /**
     * @deprecated Usar `setLongitudVo(?LongitudDecimal $lon): void` en su lugar.
     */
    public function setLongitud(?float $ilongitud = null): void
    {
        $this->ilongitud = $ilongitud;
    }

    public function getLongitudVo(): ?LongitudDecimal
    {
        return LongitudDecimal::fromNullableFloat($this->ilongitud);
    }

    public function setLongitudVo(?LongitudDecimal $lon = null): void
    {
        $this->ilongitud = $lon?->value();
    }

    /**
     *
     * @return string|null $splano_doc
     */
    /**
     * @deprecated Usar `getPlanoDocVo(): ?PlanoDocText` en su lugar.
     */
    public function getPlano_doc(): ?string
    {
        return $this->splano_doc;
    }

    /**
     *
     * @param string|null $splano_doc
     */
    /**
     * @deprecated Usar `setPlanoDocVo(?PlanoDocText $doc): void` en su lugar.
     */
    public function setPlano_doc(?string $splano_doc = null): void
    {
        $this->splano_doc = $splano_doc;
    }

    public function getPlanoDocVo(): ?PlanoDocText
    {
        return PlanoDocText::fromNullableString($this->splano_doc);
    }

    public function setPlanoDocVo(?PlanoDocText $doc = null): void
    {
        $this->splano_doc = $doc?->value();
    }

    /**
     *
     * @return string|null $splano_extension
     */
    /**
     * @deprecated Usar `getPlanoExtensionVo(): ?PlanoExtensionText` en su lugar.
     */
    public function getPlano_extension(): ?string
    {
        return $this->splano_extension;
    }

    /**
     *
     * @param string|null $splano_extension
     */
    /**
     * @deprecated Usar `setPlanoExtensionVo(?PlanoExtensionText $ext): void` en su lugar.
     */
    public function setPlano_extension(?string $splano_extension = null): void
    {
        $this->splano_extension = $splano_extension;
    }

    public function getPlanoExtensionVo(): ?PlanoExtensionText
    {
        return PlanoExtensionText::fromNullableString($this->splano_extension);
    }

    public function setPlanoExtensionVo(?PlanoExtensionText $ext = null): void
    {
        $this->splano_extension = $ext?->value();
    }

    /**
     *
     * @return string|null $splano_nom
     */
    /**
     * @deprecated Usar `getPlanoNomVo(): ?PlanoNameText` en su lugar.
     */
    public function getPlano_nom(): ?string
    {
        return $this->splano_nom;
    }

    /**
     *
     * @param string|null $splano_nom
     */
    /**
     * @deprecated Usar `setPlanoNomVo(?PlanoNameText $nom): void` en su lugar.
     */
    public function setPlano_nom(?string $splano_nom = null): void
    {
        $this->splano_nom = $splano_nom;
    }

    public function getPlanoNomVo(): ?PlanoNameText
    {
        return PlanoNameText::fromNullableString($this->splano_nom);
    }

    public function setPlanoNomVo(?PlanoNameText $nom = null): void
    {
        $this->splano_nom = $nom?->value();
    }

    /**
     *
     * @return string|null $snom_sede
     */
    /**
     * @deprecated Usar `getNomSedeVo(): ?SedeNameText` en su lugar.
     */
    public function getNom_sede(): ?string
    {
        return $this->snom_sede;
    }

    /**
     *
     * @param string|null $snom_sede
     */
    /**
     * @deprecated Usar `setNomSedeVo(?SedeNameText $nomSede): void` en su lugar.
     */
    public function setNom_sede(?string $snom_sede = null): void
    {
        $this->snom_sede = $snom_sede;
    }

    public function getNomSedeVo(): ?SedeNameText
    {
        return SedeNameText::fromNullableString($this->snom_sede);
    }

    public function setNomSedeVo(?SedeNameText $nomSede = null): void
    {
        $this->snom_sede = $nomSede?->value();
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