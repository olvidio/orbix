<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;
use src\ubis\domain\value_objects\{TipoTelecoCode, TipoTelecoName};

/**
 * Clase que implementa la entidad xd_tipo_teleco
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class TipoTeleco
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código de TipoTeleco
     */
    private ?TipoTelecoCode $tipoTeleco = null;
    /**
     * Nombre de TipoTeleco
     */
    private ?TipoTelecoName $nombreTeleco = null;
    /**
     * Ubi de TipoTeleco
     *
     * @var bool|null
     */
    private bool|null $bubi = null;
    /**
     * Persona de TipoTeleco
     *
     * @var bool|null
     */
    private bool|null $bpersona = null;
    /**
     * Id de TipoTeleco
     *
     * @var int
     */
    private int $iid;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoTeleco
     */
    public function setAllAttributes(array $aDatos): TipoTeleco
    {
        if (array_key_exists('tipo_teleco', $aDatos)) {
            $valor = $aDatos['tipo_teleco'] ?? '';
            $this->setTipoTelecoVo(isset($valor) && $valor !== '' ? new TipoTelecoCode((string)$valor) : null);
        }
        if (array_key_exists('nombre_teleco', $aDatos)) {
            $this->setNombreTelecoVo(TipoTelecoName::fromNullableString($aDatos['nombre_teleco'] ?? null));
        }
        if (array_key_exists('ubi', $aDatos)) {
            $this->setUbi(is_true($aDatos['ubi']));
        }
        if (array_key_exists('persona', $aDatos)) {
            $this->setPersona(is_true($aDatos['persona']));
        }
        if (array_key_exists('id', $aDatos)) {
            $this->setId((int)$aDatos['id']);
        }
        return $this;
    }

    // -------- VO API --------
    public function getTipoTelecoVo(): ?TipoTelecoCode
    {
        return $this->tipoTeleco;
    }

    public function setTipoTelecoVo(?TipoTelecoCode $codigo = null): void
    {
        $this->tipoTeleco = $codigo;
    }

    public function getNombreTelecoVo(): ?TipoTelecoName
    {
        return $this->nombreTeleco;
    }

    public function setNombreTelecoVo(?TipoTelecoName $nombre = null): void
    {
        $this->nombreTeleco = $nombre;
    }

    /**
     *
     * @return string $stipo_teleco
     */
    /**
     * @deprecated Usar `getTipoTelecoVo(): ?TipoTelecoCode` en su lugar.
     */
    public function getTipo_teleco(): string
    {
        return $this->tipoTeleco?->value();
    }

    /**
     *
     * @param string $stipo_teleco
     */
    /**
     * @deprecated Usar `setTipoTelecoVo(?TipoTelecoCode $codigo): void` en su lugar.
     */
    public function setTipo_teleco(string $stipo_teleco): void
    {
        $stipo_teleco = trim($stipo_teleco);
        $this->tipoTeleco = $stipo_teleco !== '' ? new TipoTelecoCode($stipo_teleco) : null;
    }

    /**
     *
     * @return string|null $snombre_teleco
     */
    /**
     * @deprecated Usar `getNombreTelecoVo(): ?TipoTelecoName` en su lugar.
     */
    public function getNombre_teleco(): ?string
    {
        return $this->nombreTeleco?->value();
    }

    /**
     *
     * @param string|null $snombre_teleco
     */
    /**
     * @deprecated Usar `setNombreTelecoVo(?TipoTelecoName $nombre): void` en su lugar.
     */
    public function setNombre_teleco(?string $snombre_teleco = null): void
    {
        $this->nombreTeleco = TipoTelecoName::fromNullableString($snombre_teleco);
    }

    /**
     *
     * @return bool|null $bubi
     */
    public function isUbi(): ?bool
    {
        return $this->bubi;
    }

    /**
     *
     * @param bool|null $bubi
     */
    public function setUbi(?bool $bubi = null): void
    {
        $this->bubi = $bubi;
    }

    /**
     *
     * @return bool|null $bpersona
     */
    public function isPersona(): ?bool
    {
        return $this->bpersona;
    }

    /**
     *
     * @param bool|null $bpersona
     */
    public function setPersona(?bool $bpersona = null): void
    {
        $this->bpersona = $bpersona;
    }

    /**
     *
     * @return int $iid
     */
    public function getId(): int
    {
        return $this->iid;
    }

    /**
     *
     * @param int $iid
     */
    public function setId(int $iid): void
    {
        $this->iid = $iid;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id';
    }

    function getDatosCampos()
    {
        $oAsignaturaSet = new Set();

        $oAsignaturaSet->add($this->getDatosTipo_teleco());
        $oAsignaturaSet->add($this->getDatosNombre_teleco());
        $oAsignaturaSet->add($this->getDatosUbi());
        $oAsignaturaSet->add($this->getDatosPersona());
        return $oAsignaturaSet->getTot();
    }

    function getDatosTipo_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_teleco');
        $oDatosCampo->setMetodoGet('getTipo_teleco');
        $oDatosCampo->setMetodoSet('setTipo_teleco');
        $oDatosCampo->setEtiqueta(_("tipo teleco"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    function getDatosNombre_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_teleco');
        $oDatosCampo->setMetodoGet('getNombre_teleco');
        $oDatosCampo->setMetodoSet('setNombre_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(20);
        return $oDatosCampo;
    }

    function getDatosUbi()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('ubi');
        $oDatosCampo->setMetodoGet('isUbi');
        $oDatosCampo->setMetodoSet('setUbi');
        $oDatosCampo->setEtiqueta(_("ubi"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosPersona()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('persona');
        $oDatosCampo->setMetodoGet('isPersona');
        $oDatosCampo->setMetodoSet('setPersona');
        $oDatosCampo->setEtiqueta(_("persona"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}