<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\HomePersonaData;
use src\personas\domain\contracts\PersonaNRepositoryInterface;

final class HomePersonaDataTest extends TestCase
{
    private mixed $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_obj_pau_desconocido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = HomePersonaData::build(['id_nom' => 1, 'obj_pau' => 'PersonaZ']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $out = HomePersonaData::build(['id_nom' => 8, 'obj_pau' => 'PersonaN']);
        $this->assertArrayHasKey('error', $out);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
