<?php

namespace src\inventario\domain\value_objects;

final class EquipajeIdsActiv
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
        // Allow comma-separated list of integers; empty string allowed? We'll allow empty -> should use fromNullableString
        if ($value !== '' && !preg_match('/^\d+(,\d+)*$/', $value)) {
            throw new \InvalidArgumentException('EquipajeIdsActiv must be a comma-separated list of integers');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return int[] Parsed list of ids
     */
    public function toArray(): array
    {
        if ($this->value === '') { return []; }
        return array_map('intval', explode(',', $this->value));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromArray(array $ids): self
    {
        $clean = array_values(array_filter(array_map('intval', $ids), fn($v) => $v > 0));
        return new self(implode(',', $clean));
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) { return null; }
        $value = trim($value);
        if ($value === '') { return null; }
        return new self($value);
    }
}
