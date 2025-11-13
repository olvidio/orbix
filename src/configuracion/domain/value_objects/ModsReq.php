<?php

namespace src\configuracion\domain\value_objects;

final class ModsReq
{
    /** @var int[] */
    private array $values;

    /**
     * @param array<int,string|int> $mods
     */
    public function __construct(array $mods)
    {
        $normalized = [];
        foreach ($mods as $m) {
            if (is_string($m)) {
                $m = trim($m);
                if ($m === '') { continue; }
                if (ctype_digit($m)) {
                    $normalized[] = (int)$m;
                }
            } elseif (is_int($m)) {
                $normalized[] = $m;
            }
        }
        $this->validate($normalized);
        $this->values = array_values(array_unique($normalized));
    }

    /**
     * @param int[] $values
     */
    private function validate(array $values): void
    {
        foreach ($values as $v) {
            if ($v <= 0) {
                throw new \InvalidArgumentException('Modulo requerido inválido');
            }
        }
    }

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return '{' . implode(',', $this->values) . '}';
    }

    /**
     * @param array<int,string|int> $mods
     */
    public static function fromArray(array $mods): self
    {
        return new self($mods);
    }

    /**
     * @param array<int,string|int>|null $mods
     */
    public static function fromNullableArray(?array $mods): ?self
    {
        if ($mods === null) { return null; }
        if ($mods === []) { return null; }
        return new self($mods);
    }
}
