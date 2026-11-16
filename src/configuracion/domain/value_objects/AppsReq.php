<?php

namespace src\configuracion\domain\value_objects;

/** Aplicaciones requeridas por un módulo: ids de aplicación (la columna `apps_req` es `integer[]`). */
final class AppsReq
{
    /** @var int[] */
    private array $values;

    /**
     * @param array<int,string|int> $apps
     */
    public function __construct(array $apps)
    {
        $normalized = [];
        foreach ($apps as $a) {
            if (is_string($a)) {
                $a = trim($a);
                if ($a === '') { continue; }
                if (!preg_match('/^-?\d+$/', $a)) {
                    throw new \InvalidArgumentException('App requerida inválida');
                }
                $a = (int)$a;
            }
            if (!is_int($a) || $a <= 0) {
                throw new \InvalidArgumentException('App requerida inválida');
            }
            $normalized[] = $a;
        }
        $this->values = array_values(array_unique($normalized));
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
        return implode(',', $this->values);
    }

    /**
     * @param array<int,string|int> $apps
     */
    public static function fromArray(array $apps): self
    {
        return new self($apps);
    }

    /**
     * @param array<int,string|int>|null $apps
     */
    public static function fromNullableArray(?array $apps): ?self
    {
        if ($apps === null) { return null; }
        if ($apps === []) { return null; }
        return new self($apps);
    }
}
