<?php

namespace src\ubis\domain\entity;

    /**
     * Representa la relación entre una Casa y una Dirección
     * Tabla: u_cross_dir_cdc
     */
class UbiDireccion
{
    private int $id_ubi;
    private int $id_direccion;
    private bool $propietario = true;
    private ?bool $principal = null; // Si es la dirección principal

    public function __construct(
        int     $id_ubi,
        int     $id_direccion,
        bool    $propietario = true,
        ?bool   $principal = null,
    )
    {
        $this->id_ubi = $id_ubi;
        $this->id_direccion = $id_direccion;
        $this->propietario = $propietario;
        $this->principal = $principal;
    }

    public function getIdUbi(): int
    {
        return $this->id_ubi;
    }

    public function getIdDireccion(): int
    {
        return $this->id_direccion;
    }

    public function isPropietario(): bool
    {
        return $this->propietario;
    }

    public function isPrincipal(): ?bool
    {
        return $this->principal;
    }
}