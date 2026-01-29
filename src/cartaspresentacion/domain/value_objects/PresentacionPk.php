<?php

namespace src\cartaspresentacion\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class PresentacionPk
{
    public function __construct(
        private int    $idUbi,
        private int    $idDireccion
    )
    {
        if (!is_numeric($idUbi)) {
            throw new \InvalidArgumentException('id_ubi debe ser numérico');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idDireccion)) {
            throw new \InvalidArgumentException('id_dirección debe numérico');
        }
    }

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_ubi'], (int)$pk['id_direccion']);
    }

    public function idUbi(): int
    {
        return $this->idUbi;
    }

    public function idDireccion(): int
    {
        return $this->idDireccion;
    }

    public function equals(self $other): bool
    {
        return $this->idUbi === $other->idUbi()
            && $this->idDireccion === $other->idDireccion();
    }

    public function __toString(): string
    {
        // Representación compacta util para logs o claves cache
        return  $this->idUbi . ':' . $this->idDireccion;
    }
}
