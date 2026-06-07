<?php

namespace src\ubis\domain\entity;

class DireccionDetalle
{
    private ?Direccion $direccion = null;
    private ?bool $principal = null;
    private ?bool $propietario = null;

    /**
     * @param array{direccion?: Direccion, principal?: bool, propietario?: bool}|null $data
     */
    public function __construct(?array $data = null)
    {
        if ($data !== null) {
            if (isset($data['direccion'])) {
                $this->direccion = $data['direccion'];
            }
            if (isset($data['principal'])) {
                $this->principal = $data['principal'];
            }
            if (isset($data['propietario'])) {
                $this->propietario = $data['propietario'];
            }
        }
    }

    public function getDireccionVo(): ?Direccion
    {
        return $this->direccion;
    }

    public function setDireccionVo(Direccion|null $direccion): void
    {
        $this->direccion = $direccion instanceof Direccion
            ? $direccion
            : throw new \InvalidArgumentException('Direccion must be an instance of Direccion');
    }

    public function isPrincipal(): ?bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): void
    {
        $this->principal = $principal;
    }

    public function isPropietario(): ?bool
    {
        return $this->propietario;
    }

    public function setPropietario(bool $propietario): void
    {
        $this->propietario = $propietario;
    }
}