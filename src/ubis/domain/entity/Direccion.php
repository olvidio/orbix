<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\domain\value_objects\{APText,
    CodigoPostalText,
    DireccionId,
    DireccionText,
    LatitudDecimal,
    LongitudDecimal,
    ObservDireccionText,
    PaisName,
    PlanoDocText,
    PlanoExtensionText,
    PlanoNameText,
    PoblacionText,
    ProvinciaText,
    SedeNameText};
use function core\is_true;

class Direccion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private DireccionId $id_direccion;

    private ?DireccionText $direccion = null;

    private ?CodigoPostalText $c_p = null;

    private PoblacionText $poblacion;

    private ?ProvinciaText $provincia = null;

    private ?APText $a_p = null;

    private ?PaisName $pais = null;

    private ?DateTimeLocal $f_direccion = null;

    private ?ObservDireccionText $observ = null;

    private ?bool $cp_dcha = null;

    private ?LatitudDecimal $latitud = null;

    private ?LongitudDecimal $longitud = null;

    private ?PlanoDocText $plano_doc = null;

    private ?PlanoExtensionText $plano_extension = null;

    private ?PlanoNameText $plano_nom = null;

    private ?SedeNameText $nom_sede = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated Usar `getIdDireccionVo(): ?\\src\\ubis\\domain\\value_objects\\DireccionId` en su lugar.
     */
    public function getId_direccion(): int
    {
        return $this->id_direccion->value();
    }


    /**
     * @deprecated Usar `setIdDireccionVo(?\\src\\ubis\\domain\\value_objects\\DireccionId $id): void` en su lugar.
     */
    public function setId_direccion(int $id_direccion): void
    {
        $this->id_direccion = DireccionId::fromNullableInt($id_direccion);
    }

    // VO API for id_direccion
    public function getIdDireccionVo(): ?DireccionId
    {
        return $this->id_direccion;
    }

    public function setIdDireccionVo(DireccionId|int|null $id = null): void
    {
        $this->id_direccion = $id instanceof DireccionId
            ? $id
            : DireccionId::fromNullableInt($id);
    }

    /**
     * @deprecated Usar `getDireccionVo(): ?DireccionText` en su lugar.
     */
    public function getDireccion(): ?string
    {
        return $this->direccion->value();
    }


    /**
     * @deprecated Usar `setDireccionVo(?DireccionText $direccion): void` en su lugar.
     */
    public function setDireccion(?string $direccion = null): void
    {
        $this->direccion = DireccionText::fromNullableString($direccion);
    }

    public function getDireccionVo(): ?DireccionText
    {
        return $this->direccion;
    }

    public function setDireccionVo(DireccionText|string|null $direccion = null): void
    {
        $this->direccion = $direccion instanceof DireccionText
            ? $direccion
            : DireccionText::fromNullableString($direccion);
    }

    /**
     * @deprecated Usar `getCodigoPostalVo(): ?CodigoPostalText` en su lugar.
     */
    public function getC_p(): ?string
    {
        return $this->c_p?->value();
    }


    /**
     * @deprecated Usar `setCodigoPostalVo(?CodigoPostalText $cp): void` en su lugar.
     */
    public function setC_p(?string $c_p = null): void
    {
        $this->c_p = CodigoPostalText::fromNullableString($c_p);
    }

    public function getCodigoPostalVo(): ?CodigoPostalText
    {
        return $this->c_p;
    }

    public function setCodigoPostalVo(CodigoPostalText|string|null $cp = null): void
    {
        $this->c_p = $cp instanceof CodigoPostalText
            ? $cp
            : CodigoPostalText::fromNullableString($cp);
    }


    /**
     * @deprecated Usar `getPoblacionVo(): PoblacionText` en su lugar.
     */
    public function getPoblacion(): string
    {
        return $this->poblacion->value();
    }


    /**
     * @deprecated Usar `setPoblacionVo(PoblacionText $poblacion): void` en su lugar.
     */
    public function setPoblacion(string $poblacion): void
    {
        $this->poblacion = PoblacionText::fromNullableString($poblacion);
    }

    public function getPoblacionVo(): PoblacionText
    {
        return $this->poblacion;
    }

    public function setPoblacionVo(PoblacionText|string|null $poblacion): void
    {
        $this->poblacion = $poblacion instanceof PoblacionText
            ? $poblacion
            : PoblacionText::fromNullableString($poblacion);
    }


    /**
     * @deprecated Usar `getProvinciaVo(): ?ProvinciaText` en su lugar.
     */
    public function getProvincia(): ?string
    {
        return $this->provincia?->value();
    }


    /**
     * @deprecated Usar `setProvinciaVo(?ProvinciaText $provincia): void` en su lugar.
     */
    public function setProvincia(?string $provincia = null): void
    {
        $this->provincia = ProvinciaText::fromNullableString($provincia);
    }

    public function getProvinciaVo(): ?ProvinciaText
    {
        return $this->provincia;
    }

    public function setProvinciaVo(ProvinciaText|string|null $provincia = null): void
    {
        $this->provincia = $provincia instanceof ProvinciaText
            ? $provincia
            : ProvinciaText::fromNullableString($provincia);
    }


    /**
     * @deprecated Usar `getAPVo(): ?APText` en su lugar.
     */
    public function getA_p(): ?string
    {
        return $this->a_p?->value();
    }


    /**
     * @deprecated Usar `setAPVo(?APText $ap): void` en su lugar.
     */
    public function setA_p(?string $a_p = null): void
    {
        $this->a_p = APText::fromNullableString($a_p);
    }

    public function getAPVo(): ?APText
    {
        return $this->a_p;
    }

    public function setAPVo(APText|string|null $ap = null): void
    {
        $this->a_p = $ap instanceof APText
            ? $ap
            : APText::fromNullableString($ap);
    }


    /**
     * @deprecated Usar `getPaisVo(): ?PaisName` en su lugar.
     */
    public function getPais(): ?string
    {
        return $this->pais?->value();
    }


    /**
     * @deprecated Usar `setPaisVo(?PaisName $pais): void` en su lugar.
     */
    public function setPais(?string $pais = null): void
    {
        $this->pais = PaisName::fromNullableString($pais);
    }

    public function getPaisVo(): ?PaisName
    {
        return $this->pais;
    }

    public function setPaisVo(PaisName|string|null $pais = null): void
    {
        $this->pais = $pais instanceof PaisName
            ? $pais
            : PaisName::fromNullableString($pais);
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
        return $this->observ?->value();
    }


    /**
     * @deprecated Usar `setObservVo(?ObservDireccionText $observ): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservDireccionText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservDireccionText
    {
        return $this->observ;
    }

    public function setObservVo(ObservDireccionText|string|null $observ = null): void
    {
        $this->observ = $observ instanceof ObservDireccionText
            ? $observ
            : ObservDireccionText::fromNullableString($observ);
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
        return $this->latitud?->value();
    }


    /**
     * @deprecated Usar `setLatitudVo(?LatitudDecimal $lat): void` en su lugar.
     */
    public function setLatitud(?float $latitud = null): void
    {
        $this->latitud = LatitudDecimal::fromNullableFloat($latitud);
    }

    public function getLatitudVo(): ?LatitudDecimal
    {
        return $this->latitud;
    }

    public function setLatitudVo(LatitudDecimal|float|null $lat = null): void
    {
        $this->latitud = $lat instanceof LatitudDecimal
            ? $lat
            : LatitudDecimal::fromNullableFloat($lat);
    }


    /**
     * @deprecated Usar `getLongitudVo(): ?LongitudDecimal` en su lugar.
     */
    public function getLongitud(): ?float
    {
        return $this->longitud?->value();
    }


    /**
     * @deprecated Usar `setLongitudVo(?LongitudDecimal $lon): void` en su lugar.
     */
    public function setLongitud(?float $longitud = null): void
    {
        $this->longitud = LongitudDecimal::fromNullableFloat($longitud);
    }

    public function getLongitudVo(): ?LongitudDecimal
    {
        return $this->longitud;
    }

    public function setLongitudVo(LongitudDecimal|float|null $lon = null): void
    {
        $this->longitud = $lon instanceof LongitudDecimal
            ? $lon
            : LongitudDecimal::fromNullableFloat($lon);
    }


    /**
     * @deprecated Usar `getPlanoDocVo(): ?PlanoDocText` en su lugar.
     */
    public function getPlano_doc(): ?string
    {
        return $this->plano_doc?->value();
    }


    /**
     * @deprecated Usar `setPlanoDocVo(?PlanoDocText $doc): void` en su lugar.
     */
    public function setPlano_doc(?string $plano_doc = null): void
    {
        $this->plano_doc = PlanoDocText::fromNullableString($plano_doc);
    }

    public function getPlanoDocVo(): ?PlanoDocText
    {
        return $this->plano_doc;
    }

    public function setPlanoDocVo(PlanoDocText|string|null $doc = null): void
    {
        $this->plano_doc = $doc instanceof PlanoDocText
            ? $doc
            : PlanoDocText::fromNullableString($doc);
    }


    /**
     * @deprecated Usar `getPlanoExtensionVo(): ?PlanoExtensionText` en su lugar.
     */
    public function getPlano_extension(): ?string
    {
        return $this->plano_extension?->value();
    }


    /**
     * @deprecated Usar `setPlanoExtensionVo(?PlanoExtensionText $ext): void` en su lugar.
     */
    public function setPlano_extension(?string $plano_extension = null): void
    {
        $this->plano_extension = PlanoExtensionText::fromNullableString($plano_extension);
    }

    public function getPlanoExtensionVo(): ?PlanoExtensionText
    {
        return $this->plano_extension;
    }

    public function setPlanoExtensionVo(PlanoExtensionText|string|null $ext = null): void
    {
        $this->plano_extension = $ext instanceof PlanoExtensionText
            ? $ext
            : PlanoExtensionText::fromNullableString($ext);
    }


    /**
     * @deprecated Usar `getPlanoNomVo(): ?PlanoNameText` en su lugar.
     */
    public function getPlano_nom(): ?string
    {
        return $this->plano_nom?->value();
    }


    /**
     * @deprecated Usar `setPlanoNomVo(?PlanoNameText $nom): void` en su lugar.
     */
    public function setPlano_nom(?string $plano_nom = null): void
    {
        $this->plano_nom = PlanoNameText::fromNullableString($plano_nom);
    }

    public function getPlanoNomVo(): ?PlanoNameText
    {
        return $this->plano_nom;
    }

    public function setPlanoNomVo(PlanoNameText|string|null $nom = null): void
    {
        $this->plano_nom = $nom instanceof PlanoNameText
            ? $nom
            : PlanoNameText::fromNullableString($nom);
    }


    /**
     * @deprecated Usar `getNomSedeVo(): ?SedeNameText` en su lugar.
     */
    public function getNom_sede(): ?string
    {
        return $this->nom_sede?->value();
    }


    /**
     * @deprecated Usar `setNomSedeVo(?SedeNameText $nomSede): void` en su lugar.
     */
    public function setNom_sede(?string $nom_sede = null): void
    {
        $this->nom_sede = SedeNameText::fromNullableString($nom_sede);
    }

    public function getNomSedeVo(): ?SedeNameText
    {
        return $this->nom_sede;
    }

    public function setNomSedeVo(SedeNameText|string|null $nomSede = null): void
    {
        $this->nom_sede = $nomSede instanceof SedeNameText
            ? $nomSede
            : SedeNameText::fromNullableString($nomSede);
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
        if ($this->a_p !== null) $txt .= $this->a_p->value() . $rtn;

        return $txt;
    }

}