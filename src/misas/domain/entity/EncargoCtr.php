<?php

namespace src\misas\domain\entity;

use src\misas\domain\value_objects\EncargoCtrId;
use src\shared\domain\traits\Hydratable;

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
    use Hydratable;


    /* ATRIBUTOS ----------------------------------------------------------------- */

    private EncargoCtrId $uuid_item;

    private int $iid_enc;
    private int $iid_ubi;

    /**
     *
     * /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated use getUuidItemVo()
     */
    public function getUuid_item(): string
    {
        return $this->uuid_item->value();
    }

    public function getUuidItemVo(): EncargoCtrId
    {
        return $this->uuid_item;
    }

    /**
     * @deprecated use setUuidItemVo()
     */
    public function setUuid_item(EncargoCtrId $uuid_item): void
    {
        $this->uuid_item = new EncargoCtrId($uuid_item);
    }

    public function setUuidItemVo(EncargoCtrId $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof EncargoCtrId
            ? $uuid_item
            : new EncargoCtrId($uuid_item);
    }


    public function getId_enc(): ?int
    {
        return $this->iid_enc;
    }


    public function setId_enc(?int $iid_enc = null): void
    {
        $this->iid_enc = $iid_enc;
    }


    public function getId_ubi(): ?int
    {
        return $this->iid_ubi;
    }


    public function setId_ubi(?int $iid_ubi = null): void
    {
        $this->iid_ubi = $iid_ubi;
    }

}