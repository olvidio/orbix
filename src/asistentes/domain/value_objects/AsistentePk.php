<?php

namespace src\asistentes\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class AsistentePk
{
    public function __construct(
        private int $idActiv,
        private int $idNom
    ) {
    }

    /**
     * @param array{id_activ: mixed, id_nom: mixed} $pk
     */
    public static function fromArray(array $pk): self
    {
        if (!isset($pk['id_activ'], $pk['id_nom']) || !is_numeric($pk['id_activ']) || !is_numeric($pk['id_nom'])) {
            throw new \InvalidArgumentException('PK de asistente inválida');
        }

        return new self((int) $pk['id_activ'], (int) $pk['id_nom']);
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
