<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
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

class Direccion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_direccion;

    private ?string $direccion = null;

    private ?string $c_p = null;

    private string $poblacion;

    private ?string $provincia = null;

    private ?string $a_p = null;

    private ?string $pais = null;

   private ?DateTimeLocal $f_direccion = null;

    private ?string $observ = null;

    private bool|null $cp_dcha = null;

    private float|null $latitud = null;

    private float|null $longitud = null;

    private ?string $plano_doc = null;

    private ?string $plano_extension = null;

    private ?string $plano_nom = null;

    private ?string $nom_sede = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated Usar `getIdDireccionVo(): ?\\src\\ubis\\domain\\value_objects\\DireccionId` en su lugar.
     */
    public function getId_direccion(): int
    {
        return $this->id_direccion;
    }


    /**
     * @deprecated Usar `setIdDireccionVo(?\\src\\ubis\\domain\\value_objects\\DireccionId $id): void` en su lugar.
     */
    public function setId_direccion(int $id_direccion): void
    {
        $this->id_direccion = $id_direccion;
    }

    // VO API for id_direccion
    public function getIdDireccionVo(): ?\src\ubis\domain\value_objects\DireccionId
    {
        return isset($this->id_direccion) && $this->id_direccion > 0 ? new \src\ubis\domain\value_objects\DireccionId($this->id_direccion) : null;
    }

    public function setIdDireccionVo(?\src\ubis\domain\value_objects\DireccionId $id = null): void
    {
        $this->id_direccion = $id?->value() ?? 0;
    }


    /**
     * @deprecated Usar `getDireccionVo(): ?DireccionText` en su lugar.
     */
    public function getDireccion(): ?string
    {
        return $this->direccion;
    }


    /**
     * @deprecated Usar `setDireccionVo(?DireccionText $direccion): void` en su lugar.
     */
    public function setDireccion(?string $direccion = null): void
    {
        $this->direccion = $direccion;
    }

    public function getDireccionVo(): ?DireccionText
    {
        return DireccionText::fromNullableString($this->direccion);
    }

    public function setDireccionVo(?DireccionText $direccion = null): void
    {
        $this->direccion = $direccion?->value();
    }


    /**
     * @deprecated Usar `getCodigoPostalVo(): ?CodigoPostalText` en su lugar.
     */
    public function getC_p(): ?string
    {
        return $this->c_p;
    }


    /**
     * @deprecated Usar `setCodigoPostalVo(?CodigoPostalText $cp): void` en su lugar.
     */
    public function setC_p(?string $c_p = null): void
    {
        $this->c_p = $c_p;
    }

    public function getCodigoPostalVo(): ?CodigoPostalText
    {
        return CodigoPostalText::fromNullableString($this->c_p);
    }

    public function setCodigoPostalVo(?CodigoPostalText $cp = null): void
    {
        $this->c_p = $cp?->value();
    }


    /**
     * @deprecated Usar `getPoblacionVo(): PoblacionText` en su lugar.
     */
    public function getPoblacion(): string
    {
        return $this->poblacion;
    }


    /**
     * @deprecated Usar `setPoblacionVo(PoblacionText $poblacion): void` en su lugar.
     */
    public function setPoblacion(string $poblacion): void
    {
        $this->poblacion = $poblacion;
    }

    public function getPoblacionVo(): PoblacionText
    {
        return new PoblacionText($this->poblacion);
    }

    public function setPoblacionVo(PoblacionText $poblacion): void
    {
        $this->poblacion = $poblacion->value();
    }


    /**
     * @deprecated Usar `getProvinciaVo(): ?ProvinciaText` en su lugar.
     */
    public function getProvincia(): ?string
    {
        return $this->provincia;
    }


    /**
     * @deprecated Usar `setProvinciaVo(?ProvinciaText $provincia): void` en su lugar.
     */
    public function setProvincia(?string $provincia = null): void
    {
        $this->provincia = $provincia;
    }

    public function getProvinciaVo(): ?ProvinciaText
    {
        return ProvinciaText::fromNullableString($this->provincia);
    }

    public function setProvinciaVo(?ProvinciaText $provincia = null): void
    {
        $this->provincia = $provincia?->value();
    }


    /**
     * @deprecated Usar `getAPVo(): ?APText` en su lugar.
     */
    public function getA_p(): ?string
    {
        return $this->a_p;
    }


    /**
     * @deprecated Usar `setAPVo(?APText $ap): void` en su lugar.
     */
    public function setA_p(?string $a_p = null): void
    {
        $this->a_p = $a_p;
    }

    public function getAPVo(): ?APText
    {
        return APText::fromNullableString($this->a_p);
    }

    public function setAPVo(?APText $ap = null): void
    {
        $this->a_p = $ap?->value();
    }


    /**
     * @deprecated Usar `getPaisVo(): ?PaisName` en su lugar.
     */
    public function getPais(): ?string
    {
        return $this->pais;
    }


    /**
     * @deprecated Usar `setPaisVo(?PaisName $pais): void` en su lugar.
     */
    public function setPais(?string $pais = null): void
    {
        $this->pais = $pais;
    }

    public function getPaisVo(): ?PaisName
    {
        return PaisName::fromNullableString($this->pais);
    }

    public function setPaisVo(?PaisName $pais = null): void
    {
        $this->pais = $pais?->value();
    }


    /**
     * @return DateTimeLocal|NullDateTimeLocal|null $f_direccion
     * @deprecated El retorno null está deprecado. Este getter aplica fallback y no devolverá null en tiempo de ejecución.
     */
    public function getF_direccion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_direccion ?? new NullDateTimeLocal;
    }


    /**
     * @param DateTimeLocal|NullDateTimeLocal|null $f_direccion
     */
    public function setF_direccion(DateTimeLocal|NullDateTimeLocal|null $f_direccion = null): void
    {
        $this->f_direccion = $f_direccion instanceof NullDateTimeLocal ? null : $f_direccion;
    }


    /**
     * @deprecated Usar `getObservVo(): ?ObservDireccionText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }


    /**
     * @deprecated Usar `setObservVo(?ObservDireccionText $observ): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservDireccionText
    {
        return ObservDireccionText::fromNullableString($this->observ);
    }

    public function setObservVo(?ObservDireccionText $observ = null): void
    {
        $this->observ = $observ?->value();
    }


    /**
     * @deprecated Usar `isCpDcha(): ?bool` en su lugar.
     */
    public function isCp_dcha(): ?bool
    {
        return $this->isCpDcha();
    }

    public function isCpDcha(): ?bool
    {
        return $this->cp_dcha;
    }


    /**
     * @deprecated Usar `setCpDcha(?bool $cpDcha = null): void` en su lugar.
     */
    public function setCp_dcha(?bool $cp_dcha = null): void
    {
        $this->setCpDcha($cp_dcha);
    }

    public function setCpDcha(?bool $cpDcha = null): void
    {
        $this->cp_dcha = $cpDcha;
    }


    /**
     * @deprecated Usar `getLatitudVo(): ?LatitudDecimal` en su lugar.
     */
    public function getLatitud(): ?float
    {
        return $this->latitud;
    }


    /**
     * @deprecated Usar `setLatitudVo(?LatitudDecimal $lat): void` en su lugar.
     */
    public function setLatitud(?float $latitud = null): void
    {
        $this->latitud = $latitud;
    }

    public function getLatitudVo(): ?LatitudDecimal
    {
        return LatitudDecimal::fromNullableFloat($this->latitud);
    }

    public function setLatitudVo(?LatitudDecimal $lat = null): void
    {
        $this->latitud = $lat?->value();
    }


    /**
     * @deprecated Usar `getLongitudVo(): ?LongitudDecimal` en su lugar.
     */
    public function getLongitud(): ?float
    {
        return $this->longitud;
    }


    /**
     * @deprecated Usar `setLongitudVo(?LongitudDecimal $lon): void` en su lugar.
     */
    public function setLongitud(?float $longitud = null): void
    {
        $this->longitud = $longitud;
    }

    public function getLongitudVo(): ?LongitudDecimal
    {
        return LongitudDecimal::fromNullableFloat($this->longitud);
    }

    public function setLongitudVo(?LongitudDecimal $lon = null): void
    {
        $this->longitud = $lon?->value();
    }


    /**
     * @deprecated Usar `getPlanoDocVo(): ?PlanoDocText` en su lugar.
     */
    public function getPlano_doc(): ?string
    {
        return $this->plano_doc;
    }


    /**
     * @deprecated Usar `setPlanoDocVo(?PlanoDocText $doc): void` en su lugar.
     */
    public function setPlano_doc(?string $plano_doc = null): void
    {
        $this->plano_doc = $plano_doc;
    }

    public function getPlanoDocVo(): ?PlanoDocText
    {
        return PlanoDocText::fromNullableString($this->plano_doc);
    }

    public function setPlanoDocVo(?PlanoDocText $doc = null): void
    {
        $this->plano_doc = $doc?->value();
    }


    /**
     * @deprecated Usar `getPlanoExtensionVo(): ?PlanoExtensionText` en su lugar.
     */
    public function getPlano_extension(): ?string
    {
        return $this->plano_extension;
    }


    /**
     * @deprecated Usar `setPlanoExtensionVo(?PlanoExtensionText $ext): void` en su lugar.
     */
    public function setPlano_extension(?string $plano_extension = null): void
    {
        $this->plano_extension = $plano_extension;
    }

    public function getPlanoExtensionVo(): ?PlanoExtensionText
    {
        return PlanoExtensionText::fromNullableString($this->plano_extension);
    }

    public function setPlanoExtensionVo(?PlanoExtensionText $ext = null): void
    {
        $this->plano_extension = $ext?->value();
    }


    /**
     * @deprecated Usar `getPlanoNomVo(): ?PlanoNameText` en su lugar.
     */
    public function getPlano_nom(): ?string
    {
        return $this->plano_nom;
    }


    /**
     * @deprecated Usar `setPlanoNomVo(?PlanoNameText $nom): void` en su lugar.
     */
    public function setPlano_nom(?string $plano_nom = null): void
    {
        $this->plano_nom = $plano_nom;
    }

    public function getPlanoNomVo(): ?PlanoNameText
    {
        return PlanoNameText::fromNullableString($this->plano_nom);
    }

    public function setPlanoNomVo(?PlanoNameText $nom = null): void
    {
        $this->plano_nom = $nom?->value();
    }


    /**
     * @deprecated Usar `getNomSedeVo(): ?SedeNameText` en su lugar.
     */
    public function getNom_sede(): ?string
    {
        return $this->nom_sede;
    }


    /**
     * @deprecated Usar `setNomSedeVo(?SedeNameText $nomSede): void` en su lugar.
     */
    public function setNom_sede(?string $nom_sede = null): void
    {
        $this->nom_sede = $nom_sede;
    }

    public function getNomSedeVo(): ?SedeNameText
    {
        return SedeNameText::fromNullableString($this->nom_sede);
    }

    public function setNomSedeVo(?SedeNameText $nomSede = null): void
    {
        $this->nom_sede = $nomSede?->value();
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
        if (isset($this->direccion)) $txt .= $this->direccion . $rtn;
        if (is_true($this->cp_dcha)) {
            if (!empty($this->poblacion)) $txt .= $this->poblacion . $spc;
            if (!empty($this->c_p)) $txt .= $this->c_p;
        } else {
            if (!empty($this->c_p)) $txt .= $this->c_p . $spc;
            if (!empty($this->poblacion)) $txt .= $this->poblacion;
        }
        $txt .= $rtn;
        if (!empty($this->a_p)) $txt .= $this->a_p . $rtn;

        return $txt;
    }

}