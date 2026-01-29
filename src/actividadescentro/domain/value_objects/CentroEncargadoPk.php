<?php

namespace src\actividadescentro\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaId;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class CentroEncargadoPk
{
    public function __construct(
        private int $idActiv,
        private int $idUbi
    )
    {
        if (!is_numeric($idActiv)) {
            throw new \InvalidArgumentException('id_activ debe ser numérico');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idUbi)) {
            throw new \InvalidArgumentException('id_ubi debe numérico');
        }
    }

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_activ'], (int)$pk['id_ubi']);
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
        // Representación compacta util para logs o claves cache
        return $this->idActiv . ':' . $this->idUbi;
    }
}
