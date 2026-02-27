<?php

namespace src\ubis\domain\value_objects;

final class DelegacionCode
{
    private ?string $value;

    public function __construct(?string $value)
    {
        $value = $value !== null ? trim($value) : null;
        if ($value === '') {
            $value = null;
        }
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(?string $value): void
    {
        if ($value === null) {
            return;
        }
        // By UI convention for codes, cap length to 6 unless otherwise specified
        if (mb_strlen($value) > 8) {
            throw new \InvalidArgumentException('DelegacionCode must be at most 6 characters');
        }
        // incluir el '+' para csr+
        if (!preg_match('/^[\+A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('DelegacionCode has invalid characters');
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function equals(DelegacionCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return new self($value);
    }
}
