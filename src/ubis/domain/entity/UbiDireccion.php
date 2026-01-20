<?php

namespace src\ubis\domain\entity;

    /**
     * Representa la relación entre una Casa y una Dirección
     * Tabla: u_cross_dir_cdc
     */
class UbiDireccion
{
    private ?int $id_ubi = null;
    private ?int $id_direccion = null;
    private ?bool $propietario = null;
    private ?bool $principal = null; // Si es la dirección principal

    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            if (isset($data['id_ubi'])) {
                $this->id_ubi = $data['id_ubi'];
            }
            if (isset($data['id_direccion'])) {
                $this->id_direccion = $data['id_direccion'];
            }
            if (isset($data['propietario'])) {
                $this->propietario = $data['propietario'];
            }
            if (isset($data['principal'])) {
                $this->principal = $data['principal'];
            }
        }
    }

    public function getIdUbi(): ?int
    {
        return $this->id_ubi;
    }

    public function setIdUbi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }

    public function getIdDireccion(): ?int
    {
        return $this->id_direccion;
    }

    public function setIdDireccion(int $id_direccion): void
    {
        $this->id_direccion = $id_direccion;
    }

    public function isPropietario(): ?bool
    {
        return $this->propietario;
    }

    public function setPropietario(bool $propietario): void
    {
        $this->propietario = $propietario;
    }

    public function isPrincipal(): ?bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): void
    {
        $this->principal = $principal;
    }
}