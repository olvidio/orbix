<?php

namespace src\personas\domain\value_objects;

use InvalidArgumentException;

final class IncCode
{

    public static function getArrayIncCode(): array
    {
        return [
            'ap' => _("ap"),
            'pa' => _("pa"),
            'ad' => _("ad"),
            'o' => _("o"),
            'fl' => _("fl"),
            'in' => _("in"),
            'el' => _("el"),
        ];
    }


    private string $value;

    public function __construct(string $value)
    {
        if (!array_key_exists($value, self::getArrayIncCode())) {
            throw new InvalidArgumentException(sprintf('<%s> no es un valor válido para la incorporación', $value));
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
