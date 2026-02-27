<?php

namespace src\personas\domain\value_objects;

use InvalidArgumentException;

final class TrasladoTipoCmbCode
{

    const dl = 'dl';
    const cr = 'cr';
    const sede = 'sede';
    const ctr = 'ctr';

    public static function getArrayTipoCambio(): array
    {
        return [
            self::dl => _("delegación"),
            self::cr => _("region"),
            self::sede => _("sede"),
            self::ctr => _("centro"),
        ];
    }

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
            throw new \InvalidArgumentException('TrasladoTipoCmbCode cannot be empty');
        }
        if (!array_key_exists($value, self::getArrayTipoCambio())) {
            throw new InvalidArgumentException(sprintf('<%s> no es un valor válido para TrasladoTipoCmbCode', $value));
        }
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

    public static function fromString(string $value): self
    {
        return new self($value);
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
