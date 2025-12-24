<?php

namespace src\cambios\domain\entity;

use function core\is_true;

/**
 * Clase que implementa la entidad av_cambios_anotados_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class CambioAnotado
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de CambioAnotado
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_schema_cambio de CambioAnotado
     *
     * @var int
     */
    private int $iid_schema_cambio;
    /**
     * Id_item_cambio de CambioAnotado
     *
     * @var int
     */
    private int $iid_item_cambio;
    /**
     * Anotado de CambioAnotado
     *
     * @var bool|null
     */
    private bool|null $banotado = null;
    /**
     * Server de CambioAnotado
     *
     * @var int
     */
    private int $iserver;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return CambioAnotado
     */
    public function setAllAttributes(array $aDatos): CambioAnotado
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
        if (array_key_exists('anotado', $aDatos)) {
            $this->setAnotado(is_true($aDatos['anotado']));
        }
        if (array_key_exists('server', $aDatos)) {
            $this->setServer($aDatos['server']);
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
     * @return bool|null $banotado
     */
    public function isAnotado(): ?bool
    {
        return $this->banotado;
    }

    /**
     *
     * @param bool|null $banotado
     */
    public function setAnotado(?bool $banotado = null): void
    {
        $this->banotado = $banotado;
    }

    /**
     *
     * @return int $iserver
     */
    public function getServer(): int
    {
        return $this->iserver;
    }

    /**
     *
     * @param int $iserver
     */
    public function setServer(int $iserver): void
    {
        $this->iserver = $iserver;
    }
}