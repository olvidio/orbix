<?php

namespace src\actividadplazas\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class PlazaPeticionPk
{
    public function __construct(
        private int $idActiv,
        private int $idNom
    )
    {
        if (!is_numeric($idActiv)) {
            throw new \InvalidArgumentException('id_activ debe ser numérico');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idNom)) {
            throw new \InvalidArgumentException('id_nom debe numérico');
        }
    }

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_activ'], (int)$pk['id_nom']);
    }

    public function idActiv(): int
    {
        return $this->idActiv;
    }

    public function idNom(): int
    {
        return $this->idNom;
    }

    public function equals(self $other): bool
    {
        return $this->idActiv === $other->idActiv()
            && $this->idNom === $other->idNom();
    }

    public function __toString(): string
    {
        // Representación compacta util para logs o claves cache
        return  $this->idActiv . ':' . $this->idNom;
    }
}
