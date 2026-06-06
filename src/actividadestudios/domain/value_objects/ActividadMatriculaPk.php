<?php

namespace src\actividadestudios\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaId;
use src\dossiers\domain\value_objects\DossierTabla;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class ActividadMatriculaPk
{
    public function __construct(
        private int $idActiv,
        private int $idNom,
        private int $idAsignatura
    ) {
    }

    /**
     * @param array<string, mixed> $pk
     */
    public static function fromArray(array $pk): self
    {
        $id_asignatura = $pk['id_asignatura'] instanceof AsignaturaId
            ? $pk['id_asignatura']->value()
            : self::toInt($pk['id_asignatura'] ?? 0);

        return new self(self::toInt($pk['id_activ'] ?? 0), self::toInt($pk['id_nom'] ?? 0), $id_asignatura);
    }

    public function idActiv(): int
    {
        return $this->idActiv;
    }

    public function idNom(): int
    {
        return $this->idNom;
    }

    public function idAsignatura(): int
    {
        return $this->idAsignatura;
    }

    public function equals(self $other): bool
    {
        return $this->idActiv === $other->idActiv()
            && $this->idNom === $other->idNom()
            && $this->idAsignatura === $other->idAsignatura();
    }

    public function __toString(): string
    {
        // Representación compacta util para logs o claves cache
        return $this->idAsignatura . ':' . $this->idNom . ':' . $this->idActiv;
    }

    private static function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
