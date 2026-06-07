<?php

namespace src\notas\domain\value_objects;

/**
 * Value Object para la clave primaria compuesta de PersonaNota
 * id_nom + id_nivel + tipo_acta
 */
final class PersonaNotaPk
{
    public function __construct(
        private int $id_nom,
        private int $id_nivel,
        private int $tipo_acta
    ) {
        if ($id_nom <= 0) {
            throw new \InvalidArgumentException('id_nom debe ser > 0');
        }
        if ($tipo_acta <= 0) {
            throw new \InvalidArgumentException('tipo_acta no puede ser vacía');
        }
    }

    /**
     * @param array{id_nom: int, id_nivel: int|\src\asignaturas\domain\value_objects\NivelId, tipo_acta: int|TipoActa|null} $pk
     */
    public static function fromArray(array $pk): self
    {
        $idNivel = $pk['id_nivel'];
        $idNivelInt = $idNivel instanceof \src\asignaturas\domain\value_objects\NivelId
            ? $idNivel->value()
            : (int) $idNivel;

        $tipoActa = $pk['tipo_acta'];
        if ($tipoActa === null) {
            $tipoActaInt = TipoActa::FORMATO_ACTA;
        } elseif ($tipoActa instanceof TipoActa) {
            $tipoActaInt = $tipoActa->value();
        } else {
            $tipoActaInt = (int) $tipoActa;
        }

        return new self((int) $pk['id_nom'], $idNivelInt, $tipoActaInt);
    }

    public function idNom(): int
    {
        return $this->id_nom;
    }

    public function idNivel(): int
    {
        return $this->id_nivel;
    }

    public function tipoActa(): int
    {
        return $this->tipo_acta;
    }

    public function equals(self $other): bool
    {
        return $this->id_nom === $other->idNom()
            && $this->id_nivel === $other->idNivel()
            && $this->tipo_acta === $other->tipoActa();
    }
}
