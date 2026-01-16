<?php

namespace src\tablonanuncios\domain\value_objects;

use core\ValueObject\Uuid;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class AnuncioId extends Uuid
{
    public static function random(): self
    {
        return new AnuncioId(parent::random());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function validate(string $value): void
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $value));
        }
    }

    public static function fromString(string $value): self
    {
        return new self(RamseyUuid::fromString($value));
    }

}