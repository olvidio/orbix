<?php

namespace src\configuracion\domain\value_objects;

final class AppsReq
{
    /** @var string[] */
    private array $values;

    /**
     * @param array<int,string|int> $apps
     */
    public function __construct(array $apps)
    {
        $normalized = [];
        foreach ($apps as $a) {
            if (is_string($a)) {
                $v = trim($a);
                if ($v === '') { continue; }
                $normalized[] = $v;
            } elseif (is_int($a)) {
                $normalized[] = (string)$a;
            }
        }
        $this->validate($normalized);
        $this->values = array_values(array_unique($normalized));
    }

    /**
     * @param string[] $values
     */
    private function validate(array $values): void
    {
        foreach ($values as $v) {
            if (mb_strlen($v) > 50) {
                throw new \InvalidArgumentException('App requerida demasiado larga');
            }
            if (!preg_match("/^[\p{L}0-9_.\-]+$/u", $v)) {
                throw new \InvalidArgumentException('Nombre de app requerida con caracteres inválidos');
            }
        }
    }

    /**
     * @return string[]
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
