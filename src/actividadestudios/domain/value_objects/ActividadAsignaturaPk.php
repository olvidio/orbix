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
    )
    {
        if (!is_numeric($idActiv)) {
            throw new \InvalidArgumentException('id_activ debe ser numérico');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idAsignatura)) {
            throw new \InvalidArgumentException('id_asignatura debe numérico');
        }
    }

    public static function fromArray(array $pk): self
    {
        $id_asignatura = $pk['id_asignatura'] instanceof AsignaturaId
            ? $pk['id_asignatura']->value()
            : $pk['id_asignatura'];
        return new self((int)$pk['id_activ'], (int)$id_asignatura);
    }

    public function IdActiv(): int
    {
        return $this->idActiv;
    }

    public function IdAsignatura(): int
    {
        $idAsignatura = $this->idAsignatura instanceof AsignaturaId
            ? $this->idAsignatura->value()
            : $this->idAsignatura;

        return $idAsignatura;
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
}
