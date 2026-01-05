<?php

namespace src\inventario\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\inventario\domain\value_objects\{WhereisDocId, WhereisItemEgmId, WhereisItemId};

class Whereis
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item_whereis;

    private int|null $id_item_egm = null;

    private int|null $id_doc = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item_whereis(): int
    {
        return $this->id_item_whereis;
    }


    public function setId_item_whereis(int $id_item_whereis): void
    {
        $this->id_item_whereis = $id_item_whereis;
    }


    public function getId_item_egm(): ?int
    {
        return $this->id_item_egm;
    }


    public function setId_item_egm(?int $id_item_egm = null): void
    {
        $this->id_item_egm = $id_item_egm;
    }


    public function getId_doc(): ?int
    {
        return $this->id_doc;
    }


    public function setId_doc(?int $id_doc = null): void
    {
        $this->id_doc = $id_doc;
    }

    // Value Object API (duplicada con legacy)
    public function getIdItemWhereisVo(): WhereisItemId
    {
        return new WhereisItemId($this->id_item_whereis);
    }

    public function setIdItemWhereisVo(?WhereisItemId $id = null): void
    {
        if ($id === null) {
            return;
        }
        $this->id_item_whereis = $id->value();
    }

    public function getIdItemEgmVo(): ?WhereisItemEgmId
    {
        return $this->id_item_egm !== null ? new WhereisItemEgmId($this->id_item_egm) : null;
    }

    public function setIdItemEgmVo(?WhereisItemEgmId $id = null): void
    {
        $this->id_item_egm = $id?->value();
    }

    public function getIdDocVo(): ?WhereisDocId
    {
        return $this->id_doc !== null ? new WhereisDocId($this->id_doc) : null;
    }

    public function setIdDocVo(?WhereisDocId $id = null): void
    {
        $this->id_doc = $id?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item_whereis';
    }
}