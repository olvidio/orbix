<?php

namespace src\procesos\domain\value_objects;

final class FaseId
{
    public const FASE_PROYECTO = 1;
    public const FASE_APROBADA = 2;
    public const FASE_TERMINADA = 3;
    public const FASE_OK_SACD = 5;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        // Permitimos positivos
        if ($value < 0) {
            throw new \InvalidArgumentException('FaseId must be a non-negative integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(FaseId $other): bool
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
