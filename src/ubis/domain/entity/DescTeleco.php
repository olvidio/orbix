<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;
use src\ubis\domain\value_objects\{TipoTelecoCode, DescTelecoOrder, DescTelecoText};

/**
 * Clase que implementa la entidad xd_desc_teleco
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class DescTeleco
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de DescTeleco
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Orden de DescTeleco
     */
    private ?DescTelecoOrder $orden = null;
    /**
     * Tipo_teleco de DescTeleco (código)
     */
    private TipoTelecoCode $idTipoTeleco;
    /**
     * Desc_teleco de DescTeleco (texto)
     */
    private ?DescTelecoText $descTeleco = null;
    /**
     * Ubi de DescTeleco
     *
     * @var bool|null
     */
    private bool|null $bubi = null;
    /**
     * Persona de DescTeleco
     *
     * @var bool|null
     */
    private bool|null $bpersona = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return DescTeleco
     */
    public function setAllAttributes(array $aDatos): DescTeleco
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('orden', $aDatos)) {
            $valor = $aDatos['orden'] ?? null;
            $this->setOrdenVo(DescTelecoOrder::fromNullable(isset($valor) && $valor !== '' ? (int)$valor : null));
        }
        if (array_key_exists('id_tipo_teleco', $aDatos)) {
            $valor = $aDatos['id_tipo_teleco'] ?? '';
            $this->setIdTipoTelecoVo(isset($valor) && $valor !== '' ? new TipoTelecoCode((string)$valor) : null);
        }
        if (array_key_exists('desc_teleco', $aDatos)) {
            $this->setDescTelecoVo(DescTelecoText::fromNullableString($aDatos['desc_teleco'] ?? null));
        }
        if (array_key_exists('ubi', $aDatos)) {
            $this->setUbi(is_true($aDatos['ubi']));
        }
        if (array_key_exists('persona', $aDatos)) {
            $this->setPersona(is_true($aDatos['persona']));
        }
        return $this;
    }

    // -------- VO API --------
    public function getOrdenVo(): ?DescTelecoOrder
    {
        return $this->orden;
    }

    public function setOrdenVo(?DescTelecoOrder $orden = null): void
    {
        $this->orden = $orden;
    }

    public function getIdTipoTelecoVo(): TipoTelecoCode
    {
        return $this->idTipoTeleco;
    }

    public function setIdTipoTelecoVo(TipoTelecoCode $codigo): void
    {
        $this->idTipoTeleco = $codigo;
    }

    public function getDescTelecoVo(): ?DescTelecoText
    {
        return $this->descTeleco;
    }

    public function setDescTelecoVo(?DescTelecoText $texto = null): void
    {
        $this->descTeleco = $texto;
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
     * @return int|null $iorden
     */
    public function getOrden(): ?int
    {
        return $this->orden?->value();
    }

    /**
     *
     * @param int|null $iorden
     */
    public function setOrden(?int $iorden = null): void
    {
        $this->orden = DescTelecoOrder::fromNullable($iorden);
    }

    public function getIdTipo_teleco(): int
    {
        return $this->idTipoTeleco?->value();
    }

    public function setIdTipo_teleco(int $id_tipo_teleco): void
    {
        $this->idTipoTeleco = new TipoTelecoCode($id_tipo_teleco);
    }

    /**
     *
     * @return string|null $sdesc_teleco
     */
    public function getDesc_teleco(): ?string
    {
        return $this->descTeleco?->value();
    }

    /**
     *
     * @param string|null $sdesc_teleco
     */
    public function setDesc_teleco(?string $sdesc_teleco = null): void
    {
        $this->descTeleco = DescTelecoText::fromNullableString($sdesc_teleco);
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


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos()
    {
        $oDescTelecoSet = new Set();

        $oDescTelecoSet->add($this->getDatosOrden());
        $oDescTelecoSet->add($this->getDatosTipo_teleco());
        $oDescTelecoSet->add($this->getDatosDesc_teleco());
        $oDescTelecoSet->add($this->getDatosUbi());
        $oDescTelecoSet->add($this->getDatosPersona());
        return $oDescTelecoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iorden de DescTeleco
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosOrden()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden');
        $oDatosCampo->setMetodoSet('setOrden');
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(2);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_teleco de DescTeleco
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getIdTipoTelecoVo');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\ubis\\application\\repositories\\TipoTelecoRepository'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombreTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposTeleco'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_teleco de DescTeleco
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDesc_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_teleco');
        $oDatosCampo->setMetodoGet('getDesc_teleco');
        $oDatosCampo->setMetodoSet('setDesc_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
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