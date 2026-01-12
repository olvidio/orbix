<?php

namespace src\shared\domain\value_objects;

final class SfsvId
{
    public const SV = 1;
    public const SF = 2;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::SV, self::SF], true)) {
            throw new \InvalidArgumentException('SfsvId solo admite 1 (sv) o 2 (sf)');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(SfsvId $other): bool
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
