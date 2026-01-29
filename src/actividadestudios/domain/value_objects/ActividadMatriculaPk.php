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
    )
    {
        if (!is_numeric($idActiv)) {
            throw new \InvalidArgumentException('id_activ debe ser numérico');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idNom)) {
            throw new \InvalidArgumentException('id_nom debe ser numérico');
        }
        if (!is_numeric($this->idAsignatura)) {
            throw new \InvalidArgumentException('id_asignatura debe ser numérico');
        }
    }

    public static function fromArray(array $pk): self
    {
        $id_asignatura = $pk['id_asignatura'] instanceof AsignaturaId
            ? $pk['id_asignatura']->value()
            : $pk['id_asignatura'];

        return new self((int)$pk['id_activ'], (int)$pk['id_nom'], (int)$id_asignatura);
    }

    public function idActiv(): int
    {
        return $this->idActiv;
    }

    public function idNom(): int
    {
        return $this->idNom;
    }

    public function idAsignatura(): string
    {
        $id_asignatura = $this->idAsignatura instanceof AsignaturaId
            ? $this->idAsignatura->value()
            : $this->idAsignatura;

        return $id_asignatura;
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
}
