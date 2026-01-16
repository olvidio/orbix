<?php

namespace src\notas\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de PersonaNota
 * id_nom + id_nivel + tipo_acta
 */
final class PersonaNotaPk
{
    public function __construct(
        private int    $id_nom,
        private int    $id_nivel,
        private int $tipo_acta
    ) {
        if (!is_numeric($id_nom)) {
            throw new \InvalidArgumentException('id_nom debe ser > 0');
        }
        // puede ser negativo para los ex
        if (!is_numeric($id_nivel)) {
            throw new \InvalidArgumentException('id_pau debe ser > 0');
        }
        if (!is_numeric($tipo_acta)) {
            throw new \InvalidArgumentException('tipo_acta no puede ser vacía');
        }
    }

    public static function fromArray(array $pk): self
    {
        return new self((int)$pk['id_nom'], (int)$pk['id_nivel'], (int)$pk['tipo_acta']);
    }

    public function idNom(): int { return $this->id_nom; }
    public function idNivel(): int { return $this->id_nivel; }
    public function tipoActa(): int { return $this->tipo_acta; }

    public function equals(self $other): bool
    {
        return $this->id_nom === $other->idNom()
            && $this->id_nivel === $other->idNivel()
            && $this->tipo_acta === $other->tipoActa();
    }

}
