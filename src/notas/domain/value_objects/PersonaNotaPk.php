<?php

namespace src\notas\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de Dossier
 * id_tipo_dossier + id_pau + tabla
 */
final class PersonaNotaPk
{
    public function __construct(
        private int $idNom,
        private int $idNivel,
        private string $tipoActa
    ) {
        if (!is_numeric($idNom)) {
            throw new \InvalidArgumentException('id_nom debe ser > 0');
        }
        // puede ser negativo para los ex
        if (!is_numeric($idNivel)) {
            throw new \InvalidArgumentException('id_pau debe ser > 0');
        }
        if (!is_numeric($idNivel)) {
            throw new \InvalidArgumentException('tabla no puede ser vacía');
        }
    }

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_tipo_dossier'], (int)$pk['id_pau'], (string)$pk['tabla']);
    }

    public function idNom(): int { return $this->idNom; }
    public function idNivel(): int { return $this->idNivel; }
    public function tipoActa(): string { return $this->tipoActa; }

    public function equals(self $other): bool
    {
        return $this->idNom === $other->idNom()
            && $this->idNivel === $other->idNivel()
            && $this->tipoActa === $other->tipoActa();
    }

}
