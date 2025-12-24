<?php

namespace src\cambios\domain\entity;

use src\cambios\domain\value_objects\AvisoTipoId;
use function core\is_true;

/**
 * Clase que implementa la entidad av_cambios_usuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class CambioUsuario
{
    // aviso tipo constants.
    const TIPO_LISTA = 1; // Anotar en lista.
    const TIPO_MAIL = 2; // por mail.

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de CambioUsuario
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_schema_cambio de CambioUsuario
     *
     * @var int
     */
    private int $iid_schema_cambio;
    /**
     * Id_item_cambio de CambioUsuario
     *
     * @var int
     */
    private int $iid_item_cambio;
    /**
     * Id_usuario de CambioUsuario
     *
     * @var int
     */
    private int $iid_usuario;
    /**
     * Sfsv de CambioUsuario
     *
     * @var int
     */
    private int $isfsv;
    /**
     * Aviso_tipo de CambioUsuario
     *
     * @var int
     */
    private int $iaviso_tipo;
    /**
     * Avisado de CambioUsuario
     *
     * @var bool|null
     */
    private bool|null $bavisado = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CambioUsuario
     */
    public function setAllAttributes(array $aDatos): CambioUsuario
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_schema_cambio', $aDatos)) {
            $this->setId_schema_cambio($aDatos['id_schema_cambio']);
        }
        if (array_key_exists('id_item_cambio', $aDatos)) {
            $this->setId_item_cambio($aDatos['id_item_cambio']);
        }
        if (array_key_exists('id_usuario', $aDatos)) {
            $this->setId_usuario($aDatos['id_usuario']);
        }
        if (array_key_exists('sfsv', $aDatos)) {
            $this->setSfsv($aDatos['sfsv']);
        }
        if (array_key_exists('aviso_tipo', $aDatos)) {
            $this->setAviso_tipo($aDatos['aviso_tipo']);
        }
        if (array_key_exists('avisado', $aDatos)) {
            $this->setAvisado(is_true($aDatos['avisado']));
        }
        return $this;
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
     * @return int $iid_schema_cambio
     */
    public function getId_schema_cambio(): int
    {
        return $this->iid_schema_cambio;
    }

    /**
     *
     * @param int $iid_schema_cambio
     */
    public function setId_schema_cambio(int $iid_schema_cambio): void
    {
        $this->iid_schema_cambio = $iid_schema_cambio;
    }

    /**
     *
     * @return int $iid_item_cambio
     */
    public function getId_item_cambio(): int
    {
        return $this->iid_item_cambio;
    }

    /**
     *
     * @param int $iid_item_cambio
     */
    public function setId_item_cambio(int $iid_item_cambio): void
    {
        $this->iid_item_cambio = $iid_item_cambio;
    }

    /**
     *
     * @return int $iid_usuario
     */
    public function getId_usuario(): int
    {
        return $this->iid_usuario;
    }

    /**
     *
     * @param int $iid_usuario
     */
    public function setId_usuario(int $iid_usuario): void
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     *
     * @return int $isfsv
     */
    public function getSfsv(): int
    {
        return $this->isfsv;
    }

    /**
     *
     * @param int $isfsv
     */
    public function setSfsv(int $isfsv): void
    {
        $this->isfsv = $isfsv;
    }

    /**
     * @return AvisoTipoId
     */
    public function getAvisoTipoVo(): AvisoTipoId
    {
        return new AvisoTipoId($this->iaviso_tipo);
    }

    /**
     * @param AvisoTipoId $avisoTipoId
     */
    public function setAvisoTipoVo(AvisoTipoId $avisoTipoId): void
    {
        $this->iaviso_tipo = $avisoTipoId->value();
    }

    /**
     * @deprecated usar getAvisoTipoVo()
     * @return int $iaviso_tipo
     */
    public function getAviso_tipo(): int
    {
        return $this->iaviso_tipo;
    }

    /**
     * @deprecated usar setAvisoTipoVo()
     * @param int $iaviso_tipo
     */
    public function setAviso_tipo(int $iaviso_tipo): void
    {
        $this->iaviso_tipo = $iaviso_tipo;
    }

    /**
     *
     * @return bool|null $bavisado
     */
    public function isAvisado(): ?bool
    {
        return $this->bavisado;
    }

    /**
     *
     * @param bool|null $bavisado
     */
    public function setAvisado(?bool $bavisado = null): void
    {
        $this->bavisado = $bavisado;
    }
}