<?php

namespace misas\domain\entity;

use misas\domain\EncargoCtrId;

/**
 * Clase que implementa la entidad misa_rel_encargo_ctr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2024
 */
class EncargoCtr
{


    /* ATRIBUTOS ----------------------------------------------------------------- */

    private EncargoCtrId $uuid_item;
    /**
     * Id_ctr de EncargoDia
     *
     * @var int
     */
    private int $iid_enc;
    private int $iid_ubi;

    /**

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return EncargoCtr
     */
    public function setAllAttributes(array $aDatos): EncargoCtr
    {
        if (array_key_exists('uuid_item', $aDatos)) {
            $uuid = new EncargoCtrId($aDatos['uuid_item']);
            $this->setUuid_item($uuid);
        }
        if (array_key_exists('id_enc', $aDatos)) {
            $this->setId_enc($aDatos['id_enc']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_enc($aDatos['id_ubi']);
        }
        return $this;
    }

    public function getUuid_item(): EncargoCtrId
    {
        return $this->uuid_item;
    }

    public function setUuid_item(EncargoCtrId $uuid_item): void
    {
        $this->uuid_item = $uuid_item;
    }

    /**
     *
     * @return int|null $iid_enc
     */
    public function getId_enc(): ?int
    {
        return $this->iid_enc;
    }

    /**
     *
     * @param int|null $iid_enc
     */
    public function setId_enc(?int $iid_enc = null): void
    {
        $this->iid_enc = $iid_enc;
    }

    /**
     *
     * @return int|null $iid_enc
     */
    public function getId_ubi(): ?int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int|null $iid_ubi
     */
    public function setId_ubi(?int $iid_ubi = null): void
    {
        $this->iid_ubi = $iid_ubi;
    }

}