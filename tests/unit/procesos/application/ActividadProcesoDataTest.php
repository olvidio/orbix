<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\procesos\application\ActividadProcesoData;

final class ActividadProcesoDataTest extends TestCase
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

    public function test_actividad_no_encontrada(): void
    {
        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadAllRepositoryInterface::class => $repo,
        ]);

        $out = ActividadProcesoData::execute(9);
        $this->assertSame(9, $out['id_activ']);
        $this->assertSame('', $out['nom_activ']);
    }

    public function test_actividad_encontrada(): void
    {
        $act = $this->createMock(ActividadAll::class);
        $act->method('getNom_activ')->willReturn('Curso X');

        $repo = $this->createMock(ActividadAllRepositoryInterface::class);
        $repo->method('findById')->with(3)->willReturn($act);

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadAllRepositoryInterface::class => $repo,
        ]);

        $out = ActividadProcesoData::execute(3);
        $this->assertSame('Curso X', $out['nom_activ']);
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
