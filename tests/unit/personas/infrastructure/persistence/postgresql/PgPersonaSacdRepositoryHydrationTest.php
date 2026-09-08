<?php

declare(strict_types=1);

namespace Tests\unit\personas\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;
use src\personas\domain\entity\PersonaSacd;
use src\personas\infrastructure\persistence\postgresql\PgPersonaSacdRepository;

final class PgPersonaSacdRepositoryHydrationTest extends TestCase
{
    public function test_create_entity_from_array_devuelve_persona_sacd(): void
    {
        $repo = new class extends PgPersonaSacdRepository {
            public function __construct()
            {
            }

            /**
             * @param array<string, mixed> $aDatos
             */
            public function hydrateForTest(array $aDatos): PersonaSacd
            {
                return $this->createEntityFromArray($aDatos);
            }
        };

        $persona = $repo->hydrateForTest([
            'id_schema' => 1,
            'id_nom' => 4411,
            'id_tabla' => 'n',
            'apellido1' => 'García',
            'situacion' => 'A',
        ]);

        $this->assertInstanceOf(PersonaSacd::class, $persona);
        $this->assertSame(4411, $persona->getId_nom());
    }
}
