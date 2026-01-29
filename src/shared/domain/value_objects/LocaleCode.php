<?php

namespace src\shared\domain\value_objects;

/*
 * Language (ca): El código ISO 639-1.
 * Territory (ES): El código de país ISO 3166-1.
 * Codeset (UTF-8): La codificación de caràcteres.
 *
 * Format: xx_XX.ENCODING (e.g., es_ES.UTF-8)
 */
final readonly class LocaleCode implements \Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $normalizedValue = $this->normalize(trim($value));
        $this->validate($normalizedValue);
        $this->value = $normalizedValue;
    }

    private function normalize(string $value): string
    {
        // Dividimos para formatear cada parte (xx_XX.ENCODING)
        if (preg_match('/^([a-z]{2})_([a-z]{2})\.(.+)$/i', $value, $matches)) {
            return strtolower($matches[1]) . '_' . strtoupper($matches[2]) . '.' . strtoupper($matches[3]);
        }
        return $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('LocaleCode cannot be empty');
        }

        // Expresión regular ajustada para ser un poco más flexible con el encoding
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}\.[A-Z0-9\-]+$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Invalid LocaleCode format: "%s". Expected xx_XX.ENCODING', $value));
        }
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        return new self($value);
    }

    // Ejemplo de Factory Method para valores comunes
    public static function spanish(): self
    {
        return new self('es_ES.UTF-8');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(LocaleCode $other): bool
    {
        return $this->value === $other->value;
    }
}
