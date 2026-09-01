<?php

namespace src\notas\domain\value_objects;

final class TipoActa
{
    // tipo_acta constants.
    public const FORMATO_ACTA = 1; // Acta.
    public const FORMATO_CERTIFICADO = 2; // Certificado.

    public const ARRAY_TIPO_ACTA = [
        self::FORMATO_ACTA,
        self::FORMATO_CERTIFICADO];

    private int $value;

    public function __construct(int $value)
    {
        if (!in_array($value, self::ARRAY_TIPO_ACTA)) {
            throw new \InvalidArgumentException(sprintf('<%s> no es un valor válido para TipoActa', $value));
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        // 0 = vacío (POST, PDO, datos legacy). En SQL se usa COALESCE(tipo_acta, 1).
        if ($value === 0) {
            return new self(self::FORMATO_ACTA);
        }
        return new self($value);
    }

    /**
     * Hidratación desde PostgreSQL: NULL, 0 y vacío equivalen a acta.
     */
    public static function fromPg(mixed $value): self
    {
        if ($value === null || $value === '' || $value === false) {
            return new self(self::FORMATO_ACTA);
        }
        if (!is_numeric($value)) {
            return new self(self::FORMATO_ACTA);
        }

        return self::fromNullableInt((int) $value) ?? new self(self::FORMATO_ACTA);
    }
}
