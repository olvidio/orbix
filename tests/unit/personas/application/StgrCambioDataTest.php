<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\StgrCambioData;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;

final class StgrCambioDataTest extends TestCase
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

    public function test_id_tabla_vacio(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = StgrCambioData::build(['id_nom' => 1, 'id_tabla' => '']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_id_tabla_desconocido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $out = StgrCambioData::build(['id_nom' => 1, 'id_tabla' => 'zzz']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $out = StgrCambioData::build(['id_nom' => 99, 'id_tabla' => 'n']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_exito(): void
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getNombreApellidos')->willReturn('Nom Ap');
        $p->method('getNivel_stgr')->willReturn('2');

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn($p);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $out = StgrCambioData::build(['id_nom' => 5, 'id_tabla' => 'n']);
        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame('Nom Ap', $out['nom']);
        $this->assertSame('2', $out['nivel_stgr']);
        $this->assertSame(5, $out['id_nom']);
        $this->assertSame('n', $out['id_tabla']);
        $this->assertIsArray($out['opciones_nivel_stgr']);
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
