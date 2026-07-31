<?php

declare(strict_types=1);

namespace src\personas\domain\traits;

use src\personas\domain\PersonaPublicacion;
use src\shared\domain\traits\Hydratable;

/**
 * Hydratable + serialización de `publicado_para` como jsonb (JSON), no array PG.
 */
trait SerializesPublicadoParaJson
{
    use Hydratable {
        toArrayForDatabase as private hydratableToArrayForDatabase;
    }

    /**
     * @param array<string, callable(mixed): mixed> $converters
     * @return array<string, mixed>
     */
    public function toArrayForDatabase(array $converters = []): array
    {
        $converters['publicado_para'] ??= static fn(mixed $v): ?string => PersonaPublicacion::toDatabaseValue($v);

        return $this->hydratableToArrayForDatabase($converters);
    }
}
