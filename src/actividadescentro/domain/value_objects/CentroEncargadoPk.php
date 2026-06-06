<?php

namespace src\actividadescentro\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de CentroEncargado
 * id_activ + id_ubi
 */
final class CentroEncargadoPk
{
    public function __construct(
        private int $idActiv,
        private int $idUbi
    ) {
    }

    /**
     * @param array{id_activ: int|string, id_ubi: int|string} $pk
     */
    public static function fromArray(array $pk): self
    {
        return new self((int) $pk['id_activ'], (int) $pk['id_ubi']);
    }

    public function IdActiv(): int
    {
        return $this->idActiv;
    }

    public function IdUbi(): int
    {
        return $this->idUbi;
    }

    public function equals(self $other): bool
    {
        return $this->idActiv === $other->IdActiv()
            && $this->idUbi === $other->IdUbi();
    }

    public function __toString(): string
    {
        return $this->idActiv . ':' . $this->idUbi;
    }
}
