<?php
namespace src\notas\domain\value_objects;

final class NotaNum
{
    private int $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function value(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullableFloat(?float $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
