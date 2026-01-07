<?php

namespace src\encargossacd\domain\value_objects;

final class EncargoModHorarioId
{
    // modo horario constants.
    //1: opcional , 2:por módulos, 3: por horario
    public const HORARIO_OPCIONAL = 1; // opcional
    public const HORARIO_POR_MODULOS = 2; // Por módulos (mañana, tarde 1ª hora, tarde 2ª hora).
    public const HORARIO_POR_HORAS = 3; // Por horario (día y hora).

    public const ARRAY_HORARIO_TXT = [
        self::HORARIO_OPCIONAL => "opcional",
        self::HORARIO_POR_MODULOS => "módulos",
        self::HORARIO_POR_HORAS => "día y hora",
    ];

    private int $value;

    public function __construct(int $value)
    {
        if (!array_key_exists($value, self::ARRAY_HORARIO_TXT)) {
            throw new \InvalidArgumentException(sprintf('<%s> no es un valor válido para EncargoModHorarioId', $value));
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(EncargoModHorarioId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
