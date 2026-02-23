<?php

namespace src\procesos\domain\value_objects;

final class ActividadId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('ActividadId must be integer');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(ActividadId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
