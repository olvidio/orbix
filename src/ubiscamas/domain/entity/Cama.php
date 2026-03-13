<?php

namespace src\ubiscamas\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubiscamas\domain\value_objects\{CamaId,
    CamaDescripcion};

class Cama
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ?int $id_schema = null;

    private CamaId $id_cama;

    private int $id_habitacion;

    private CamaDescripcion $descripcion;

    private ?bool $larga = null;

    private ?bool $vip = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getIdSchema(): ?int
    {
        return $this->id_schema;
    }

    public function setIdSchema(?int $id_schema = null): void
    {
        $this->id_schema = $id_schema;
    }

    /**
     * @deprecated Usar `getIdCamaVo(): CamaId` en su lugar.
     */
    public function getIdCama(): string
    {
        return $this->id_cama->value();
    }

    /**
     * @deprecated Usar `setIdCamaVo(CamaId $id): void` en su lugar.
     */
    public function setIdCama(string $id_cama): void
    {
        $this->id_cama = new CamaId($id_cama);
    }

    public function getIdCamaVo(): CamaId
    {
        return $this->id_cama;
    }

    public function setIdCamaVo(CamaId|string|null $id): void
    {
        $this->id_cama = $id instanceof CamaId
            ? $id
            : CamaId::fromNullableString($id);
    }

    /**
     * @deprecated Usar `getIdHabitacionVo(): int` en su lugar.
     */
    public function getIdHabitacion(): int
    {
        return $this->id_habitacion;
    }

    /**
     * @deprecated Usar `setIdHabitacionVo(int $id): void` en su lugar.
     */
    public function setIdHabitacion(int $id_habitacion): void
    {
        $this->id_habitacion = $id_habitacion;
    }

    public function getIdHabitacionVo(): int
    {
        return $this->id_habitacion;
    }

    public function setIdHabitacionVo(int $id): void
    {
        $this->id_habitacion = $id;
    }

    /**
     * @deprecated Usar `getDescripcionVo(): CamaDescripcion` en su lugar.
     */
    public function getDescripcion(): string
    {
        return $this->descripcion->value();
    }

    /**
     * @deprecated Usar `setDescripcionVo(CamaDescripcion $descripcion): void` en su lugar.
     */
    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = new CamaDescripcion($descripcion);
    }

    public function getDescripcionVo(): CamaDescripcion
    {
        return $this->descripcion;
    }

    public function setDescripcionVo(CamaDescripcion|string $texto): void
    {
        $this->descripcion = $texto instanceof CamaDescripcion
            ? $texto
            : new CamaDescripcion($texto);
    }

    public function isLarga(): ?bool
    {
        return $this->larga;
    }

    public function setLarga(?bool $larga = null): void
    {
        $this->larga = $larga;
    }

    public function isVip(): ?bool
    {
        return $this->vip;
    }

    public function setVip(?bool $vip = null): void
    {
        $this->vip = $vip;
    }
}
