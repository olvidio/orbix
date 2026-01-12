<?php

namespace src\shared\domain\value_objects;

/**
 * Value Object de dinero con precisión de 2 decimales (almacenado en céntimos)
 */
final class Dinero
{
    private int $cents;

    /**
     * @param int|float|string $value Acepta céntimos (int), decimal (float/string)
     */
    public function __construct(int|float|string $value)
    {
        $this->cents = self::normalizeToCents($value);
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }

    public static function fromNullableFloat(?float $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }

    public static function fromString(string $amount): self
    {
        return new self($amount);
    }

    public function asCents(): int
    {
        return $this->cents;
    }

    public function asFloat(): float
    {
        return $this->cents / 100.0;
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function asString(): string
    {
        $sign = $this->cents < 0 ? '-' : '';
        $abs = abs($this->cents);
        $euros = intdiv($abs, 100);
        $cent = $abs % 100;
        return sprintf('%s%d.%02d', $sign, $euros, $cent);
    }

    public function equals(Dinero $other): bool
    {
        return $this->cents === $other->asCents();
    }

    private static function normalizeToCents(int|float|string $value): int
    {
        if (is_int($value)) {
            // Interpretamos ints como céntimos directamente
            return $value;
        }
        if (is_float($value)) {
            // Evitar problemas binarios: redondear a 2 decimales y convertir a céntimos
            return (int)round($value * 100, 0);
        }
        // string: normalizar separadores y validar dos decimales
        $s = trim($value);
        // admitir coma como separador decimal
        $s = str_replace([','], ['.'], $s);
        if ($s === '') {
            throw new \InvalidArgumentException('Dinero no puede ser vacío');
        }
        if (!preg_match('/^[+-]?\d+(?:\.\d{1,2})?$/', $s)) {
            throw new \InvalidArgumentException('Dinero debe tener como máximo 2 decimales');
        }
        // usar BCMath si disponible, sino parse manual
        $parts = explode('.', $s, 2);
        $sign = 1;
        if (str_starts_with($parts[0], '+')) {
            $parts[0] = ltrim($parts[0], '+');
        } elseif (str_starts_with($parts[0], '-')) {
            $sign = -1;
            $parts[0] = ltrim($parts[0], '-');
        }
        $euros = (int)$parts[0];
        $cents = 0;
        if (isset($parts[1])) {
            $dec = str_pad($parts[1], 2, '0');
            $dec = substr($dec, 0, 2);
            $cents = (int)$dec;
        }
        return $sign * ($euros * 100 + $cents);
    }
}
