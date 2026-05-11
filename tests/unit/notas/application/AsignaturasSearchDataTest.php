<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\AsignaturasSearchData;

final class AsignaturasSearchDataTest extends TestCase
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

    public function test_delega_en_repositorio(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getJsonAsignaturas')
            ->with(['nombre_asignatura' => 'mat'])
            ->willReturn('[{"label":"x"}]');

        $GLOBALS['container'] = $this->containerFromMap([
            AsignaturaRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('[{"label":"x"}]', AsignaturasSearchData::execute(['search' => 'mat']));
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
