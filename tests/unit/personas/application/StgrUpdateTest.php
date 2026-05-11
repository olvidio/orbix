<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\StgrUpdate;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;

final class StgrUpdateTest extends TestCase
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

    public function test_id_tabla_desconocido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $this->assertNotSame('', StgrUpdate::execute(1, 'bad', 1));
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', StgrUpdate::execute(2, 'n', 1));
    }

    public function test_exito(): void
    {
        $p = $this->createMock(PersonaN::class);
        $p->expects($this->once())->method('setNivel_stgr')->with(3);

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn($p);
        $repo->expects($this->once())->method('Guardar')->with($p)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertSame('', StgrUpdate::execute(1, 'n', 3));
    }

    public function test_falla_guardar(): void
    {
        $p = $this->createMock(PersonaN::class);

        $repo = $this->getMockBuilder(PgPersonaNRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findById', 'Guardar', 'getErrorTxt'])
            ->getMock();
        $repo->method('findById')->willReturn($p);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('stgr-db');

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertStringContainsString('stgr-db', StgrUpdate::execute(1, 'n', 0));
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
