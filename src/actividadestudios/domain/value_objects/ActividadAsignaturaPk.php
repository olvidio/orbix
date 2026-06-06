<?php

namespace src\actividadestudios\domain\value_objects;

use src\asignaturas\domain\value_objects\AsignaturaId;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class ActividadAsignaturaPk
{
    public function __construct(
        private int $idActiv,
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

        return new self(self::toInt($pk['id_activ'] ?? 0), $id_asignatura);
    }

    public function IdActiv(): int
    {
        return $this->idActiv;
    }

    public function IdAsignatura(): int
    {
        return $this->idAsignatura;
    }

    public function equals(self $other): bool
    {
        return $this->idActiv === $other->IdActiv()
            && $this->idAsignatura === $other->IdAsignatura();
    }

    public function __toString(): string
    {
        // Representación compacta util para logs o claves cache
        return $this->idActiv . ':' . $this->idAsignatura;
    }

    private static function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
