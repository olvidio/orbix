<?php

namespace src\inventario\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\inventario\domain\value_objects\{WhereisDocId, WhereisItemEgmId, WhereisItemId};

class Whereis
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item_whereis;

    private ?WhereisItemEgmId $id_item_egm = null;

    private ?WhereisDocId $id_doc = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item_whereis(): int
    {
        return $this->id_item_whereis;
    }


    public function setId_item_whereis(int $id_item_whereis): void
    {
        $this->id_item_whereis = $id_item_whereis;
    }


    public function getId_item_egm(): ?string
    {
        return $this->id_item_egm?->value();
    }


    public function setId_item_egm(?int $id_item_egm = null): void
    {
        $this->id_item_egm = WhereisItemEgmId::fromNullable($id_item_egm);
    }


    public function getId_doc(): ?string
    {
        return $this->id_doc?->value();
    }


    public function setId_doc(?int $id_doc = null): void
    {
        $this->id_doc = WhereisDocId::fromNullable($id_doc);
    }

    public function getIdItemEgmVo(): ?WhereisItemEgmId
    {
        return $this->id_item_egm;
    }

    public function setIdItemEgmVo(WhereisItemEgmId|int|null $id = null): void
    {
        $this->id_item_egm = $id instanceof WhereisItemEgmId
            ? $id
            : WhereisItemEgmId::fromNullable($id);
    }

    public function getIdDocVo(): ?WhereisDocId
    {
        return $this->id_doc;
    }

    public function setIdDocVo(WhereisDocId|int|null $id = null): void
    {
        $this->id_doc = $id instanceof WhereisDocId
            ? $id
            : WhereisDocId::fromNullable($id);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item_whereis';
    }
}