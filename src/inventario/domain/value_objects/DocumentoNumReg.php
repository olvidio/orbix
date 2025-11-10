<?php

namespace src\inventario\domain\value_objects;

final class DocumentoNumReg
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('DocumentoNumReg must be zero or positive');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(DocumentoNumReg $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        if ($value === '' || !preg_match('/^-?\\d+$/', $value)) {
            throw new \InvalidArgumentException('DocumentoNumReg string must be an integer');
        }
        return new self((int)$value);
    }
}
