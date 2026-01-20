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

    private int $id_enc;
    private int $id_ubi;

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
    public function setUuid_item(string $uuid_item): void
    {
        $this->uuid_item = new EncargoCtrId($uuid_item);
    }

    public function setUuidItemVo(EncargoCtrId|string $uuid_item): void
    {
        $this->uuid_item = $uuid_item instanceof EncargoCtrId
            ? $uuid_item
            : new EncargoCtrId($uuid_item);
    }


    public function getId_enc(): ?int
    {
        return $this->id_enc;
    }


    public function setId_enc(?int $id_enc = null): void
    {
        $this->id_enc = $id_enc;
    }


    public function getId_ubi(): ?int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(?int $id_ubi = null): void
    {
        $this->id_ubi = $id_ubi;
    }

}