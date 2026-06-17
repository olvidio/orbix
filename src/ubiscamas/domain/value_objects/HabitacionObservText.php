<?php
declare(strict_types=1);

namespace src\ubiscamas\domain\value_objects;

use InvalidArgumentException;
use src\shared\domain\value_objects\ValueObjectMessages;

/**
 * Value Object para las observaciones de una habitación.
 * Máximo 250 caracteres.
 */
final class HabitacionObservText
{
    private string $value;

    public function __construct(string $value)
    {
        if (mb_strlen($value) > 250) {
            throw new InvalidArgumentException(ValueObjectMessages::withValueContext(
                'Las observaciones no pueden superar los 250 caracteres',
                $value
            ));
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }
        return new self($value);
    }
}
