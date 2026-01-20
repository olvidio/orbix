<?php

namespace src\ubis\domain\value_objects;

final class ObservTelecoText
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('ObservTelecoText cannot be empty');
        }
        if (mb_strlen($value) > 300) {
            throw new \InvalidArgumentException('ObservTelecoText must be at most 300 characters');
        }
        // Permitir caracteres comunes de texto corto
        if (!preg_match("/^[\p{L}0-9 .,'’:_\-()\+#\/]*$/u", $value)) {
            throw new \InvalidArgumentException('ObservTelecoText has invalid characters');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ObservTelecoText $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
