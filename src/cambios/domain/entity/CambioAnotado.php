<?php

namespace src\cambios\domain\entity;

use src\shared\domain\traits\Hydratable;
use function core\is_true;

class CambioAnotado
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_schema_cambio;

    private int $id_item_cambio;

    private ?bool $anotado = null;

    private int $server;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_schema_cambio(): int
    {
        return $this->id_schema_cambio;
    }


    public function setId_schema_cambio(int $id_schema_cambio): void
    {
        $this->id_schema_cambio = $id_schema_cambio;
    }


    public function getId_item_cambio(): int
    {
        return $this->id_item_cambio;
    }


    public function setId_item_cambio(int $id_item_cambio): void
    {
        $this->id_item_cambio = $id_item_cambio;
    }


    public function isAnotado(): ?bool
    {
        return $this->anotado;
    }


    public function setAnotado(?bool $anotado = null): void
    {
        $this->anotado = $anotado;
    }


    public function getServer(): int
    {
        return $this->server;
    }


    public function setServer(int $server): void
    {
        $this->server = $server;
    }
}