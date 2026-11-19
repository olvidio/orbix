<?php

declare(strict_types=1);

namespace src\shared\domain\value_objects;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Identificador de una consulta hecha a la ayuda ({@see \src\shared\domain\entity\AyudaConsulta}).
 */
final class AyudaConsultaId extends Uuid
{
    public static function random(): self
    {
        return new self(RamseyUuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /** `null` si el texto está vacío o no es un uuid. */
    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || !RamseyUuid::isValid($value)) {
            return null;
        }

        return new self($value);
    }
}
