<?php

namespace src\shared\domain\value_objects;

final class SfsvOtrosId
{
    public const SV = 1;
    public const SF = 2;
    public const Otros = 3;

    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::SV, self::SF, self::Otros], true)) {
            throw new \InvalidArgumentException('SfsvId solo admite 1 (sv), 2 (sf), 3 (otros)');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(SfsvOtrosId $other): bool
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
