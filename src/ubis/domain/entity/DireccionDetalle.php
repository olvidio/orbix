<?php

namespace src\ubis\domain\entity;

class DireccionDetalle
{
    private Direccion $direccion;
    private bool $principal;
    private bool $propietario;

    public function __construct(Direccion $direccion, bool $principal, bool $propietario)
    {
        $this->direccion = $direccion;
        $this->principal = $principal;
        $this->propietario = $propietario;
    }

    public function getDireccion(): Direccion
    {
        return $this->direccion;
    }

    public function isPrincipal(): bool
    {
        return $this->principal;
    }

    public function isPropietario(): bool
    {
        return $this->propietario;
    }
}
