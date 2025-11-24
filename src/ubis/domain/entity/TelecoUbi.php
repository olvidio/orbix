<?php

namespace src\ubis\domain\entity;


use core\DatosCampo;
use core\Set;
use src\ubis\domain\value_objects\{DescTelecoText};
use src\ubis\domain\value_objects\{TipoTelecoId, TelecoUbiId, TelecoUbiItemId, NumTelecoText, ObservTelecoText};

/**
 * Clase que implementa la entidad d_teleco_cdc
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class TelecoUbi
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_ubi de TelecoCdc
     *
     * @var int
     */
    private int $iid_ubi;
    /**
     * Id_tipo_teleco de TelecoCdc
     *
     * @var int
     */
    private int $id_tipo_teleco;
    /**
     * Desc_teleco de TelecoCdc
     *
     * @var string|null
     */
    private string|null $sdesc_teleco = null;
    /**
     * Num_teleco de TelecoCdc
     *
     * @var string
     */
    private string $snum_teleco;
    /**
     * Observ de TelecoCdc
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Id_item de TelecoCdc
     *
     * @var int
     */
    private int $iid_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TelecoUbi
     */
    public function setAllAttributes(array $aDatos): TelecoUbi
    {
        if (array_key_exists('id_ubi', $aDatos)) {
            $valor = $aDatos['id_ubi'];
            if ($valor instanceof TelecoUbiId) {
                $this->setIdUbiVo($valor);
            } else {
                $this->setId_ubi((int)$valor);
            }
        }
        if (array_key_exists('id_tipo_teleco', $aDatos)) {
            $valor = $aDatos['id_tipo_teleco'];
            if ($valor instanceof TipoTelecoId) {
                $this->setIdTipoTelecoVo($valor);
            } else {
                $this->setId_tipo_teleco((int)$valor);
            }
        }
        if (array_key_exists('desc_teleco', $aDatos)) {
            $valor = $aDatos['desc_teleco'];
            if ($valor instanceof DescTelecoText || $valor === null) {
                $this->setDescTelecoVo($valor);
            } else {
                $this->setDesc_teleco($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('num_teleco', $aDatos)) {
            $valor = $aDatos['num_teleco'];
            if ($valor instanceof NumTelecoText) {
                $this->setNumTelecoVo($valor);
            } else {
                $this->setNum_teleco((string)$valor);
            }
        }
        if (array_key_exists('observ', $aDatos)) {
            $valor = $aDatos['observ'];
            if ($valor instanceof ObservTelecoText || $valor === null) {
                $this->setObservVo($valor);
            } else {
                $this->setObserv($valor !== null ? (string)$valor : null);
            }
        }
        if (array_key_exists('id_item', $aDatos)) {
            $valor = $aDatos['id_item'];
            if ($valor instanceof TelecoUbiItemId) {
                $this->setIdItemVo($valor);
            } else {
                $this->setId_item((int)$valor);
            }
        }
        return $this;
    }

    // -------- API VO (nueva) ---------
    /**
     * Getter VO para id_ubi
     */
    public function getIdUbiVo(): TelecoUbiId
    {
        return new TelecoUbiId($this->iid_ubi);
    }

    /**
     * Setter VO para id_ubi
     */
    public function setIdUbiVo(TelecoUbiId $id): void
    {
        $this->iid_ubi = $id->value();
    }

    /**
     *
     * @return int $iid_ubi
     *
     * @deprecated Usar `getIdUbiVo(): TelecoUbiId` en su lugar.
     */
    public function getId_ubi(): int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int $iid_ubi
     *
     * @deprecated Usar `setIdUbiVo(TelecoUbiId $id): void` en su lugar.
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return int $id_tipo_teleco
     *
     * @deprecated Usar `getIdTipoTelecoVo(): TipoTelecoId` en su lugar.
     */
    public function getId_tipo_teleco(): int
    {
        return $this->id_tipo_teleco;
    }

    /**
     *
     * @param int $id_tipo_teleco
     *
     * @deprecated Usar `setIdTipoTelecoVo(TipoTelecoId $id): void` en su lugar.
     */
    public function setId_tipo_teleco(int $id_tipo_teleco): void
    {
        $this->id_tipo_teleco = $id_tipo_teleco;
    }

    public function getIdTipoTelecoVo(): TipoTelecoId
    {
        return new TipoTelecoId($this->id_tipo_teleco);
    }

    public function setIdTipoTelecoVo(TipoTelecoId $id): void
    {
        $this->id_tipo_teleco = $id->value();
    }

    /**
     *
     * @return string|null $sdesc_teleco
     *
     * @deprecated Usar `getDescTelecoVo(): ?DescTelecoText` en su lugar.
     */
    public function getDesc_teleco(): ?string
    {
        return $this->sdesc_teleco;
    }

    /**
     *
     * @param string|null $sdesc_teleco
     *
     * @deprecated Usar `setDescTelecoVo(?DescTelecoText $texto = null): void` en su lugar.
     */
    public function setDesc_teleco(?string $sdesc_teleco = null): void
    {
        $this->sdesc_teleco = $sdesc_teleco;
    }

    public function getDescTelecoVo(): ?DescTelecoText
    {
        return DescTelecoText::fromNullableString($this->sdesc_teleco);
    }

    public function setDescTelecoVo(?DescTelecoText $texto = null): void
    {
        $this->sdesc_teleco = $texto?->value();
    }

    /**
     *
     * @return string $snum_teleco
     *
     * @deprecated Usar `getNumTelecoVo(): NumTelecoText` en su lugar.
     */
    public function getNum_teleco(): string
    {
        return $this->snum_teleco;
    }

    /**
     *
     * @param string $snum_teleco
     *
     * @deprecated Usar `setNumTelecoVo(NumTelecoText $texto): void` en su lugar.
     */
    public function setNum_teleco(string $snum_teleco): void
    {
        $this->snum_teleco = $snum_teleco;
    }

    public function getNumTelecoVo(): NumTelecoText
    {
        return new NumTelecoText($this->snum_teleco);
    }

    public function setNumTelecoVo(NumTelecoText $texto): void
    {
        $this->snum_teleco = $texto->value();
    }

    /**
     *
     * @return string|null $sobserv
     *
     * @deprecated Usar `getObservVo(): ?ObservTelecoText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     *
     * @deprecated Usar `setObservVo(?ObservTelecoText $texto = null): void` en su lugar.
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    public function getObservVo(): ?ObservTelecoText
    {
        return ObservTelecoText::fromNullableString($this->sobserv);
    }

    public function setObservVo(?ObservTelecoText $texto = null): void
    {
        $this->sobserv = $texto?->value();
    }

    /**
     *
     * @return int $iid_item
     *
     * @deprecated Usar `getIdItemVo(): TelecoUbiItemId` en su lugar.
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     *
     * @deprecated Usar `setIdItemVo(TelecoUbiItemId $id): void` en su lugar.
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    public function getIdItemVo(): TelecoUbiItemId
    {
        return new TelecoUbiItemId($this->iid_item);
    }

    public function setIdItemVo(TelecoUbiItemId $id): void
    {
        $this->iid_item = $id->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }
    function getDatosCampos()
    {
        $oTelecoUbiSet = new Set();

        $oTelecoUbiSet->add($this->getDatosTipo_teleco());
        $oTelecoUbiSet->add($this->getDatosDesc_teleco());
        $oTelecoUbiSet->add($this->getDatosNum_teleco());
        $oTelecoUbiSet->add($this->getDatosObserv());
        return $oTelecoUbiSet->getTot();
    }

    function getDatosTipo_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_teleco');
        $oDatosCampo->setMetodoGet('getId_tipo_teleco');
        $oDatosCampo->setMetodoSet('setId_tipo_teleco');
        $oDatosCampo->setEtiqueta(_("nombre teleco"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\ubis\\application\\repositories\\TipoTelecoRepository'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombreTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayTiposTelecoUbi'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        $oDatosCampo->setAccion('desc_teleco'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    function getDatosDesc_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('desc_teleco');
        $oDatosCampo->setMetodoGet('getDesc_teleco');
        $oDatosCampo->setMetodoSet('setDec_teleco');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument('src\ubis\application\repositories\DescTelecoRepository');
        $oDatosCampo->setArgument2('getDescTelecoVo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayDescTeleco');
        $oDatosCampo->setDepende('id_tipo_teleco');
        return $oDatosCampo;
    }

    function getDatosNum_teleco()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_teleco');
        $oDatosCampo->setMetodoGet('getNum_teleco');
        $oDatosCampo->setMetodoSet('setNunm_teleco');
        $oDatosCampo->setEtiqueta(_("número o siglas"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    function getDatosObserv()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ');
        $oDatosCampo->setMetodoGet('getObserv');
        $oDatosCampo->setMetodoSet('setObserv');
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }
}