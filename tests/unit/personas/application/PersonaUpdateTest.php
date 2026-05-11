<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaUpdate;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;

final class PersonaUpdateTest extends TestCase
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

    public function test_sin_id_nom(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $this->createMock(PersonaNRepositoryInterface::class),
        ]);

        $this->assertNotSame('', PersonaUpdate::execute(['id_nom' => 0, 'obj_pau' => 'PersonaN']));
    }

    public function test_obj_pau_desconocido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $this->assertNotSame('', PersonaUpdate::execute(['id_nom' => 1, 'obj_pau' => 'PersonaX']));
    }

    public function test_exito_actualiza_mock(): void
    {
        $persona = $this->createMock(PersonaN::class);

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(50)->willReturn($persona);
        $repo->expects($this->once())->method('Guardar')->with($persona)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $input = [
            'id_nom' => 50,
            'obj_pau' => 'PersonaN',
            'situacion' => 'A',
            'nom' => 'Ana',
            'apel_fam' => 'G',
            'nx1' => '',
            'apellido1' => 'P',
            'nx2' => '',
            'apellido2' => '',
        ];

        $this->assertSame('', PersonaUpdate::execute($input));
    }

    public function test_falla_guardar(): void
    {
        $persona = $this->createMock(PersonaN::class);

        $repo = $this->getMockBuilder(PgPersonaNRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findById', 'Guardar', 'getErrorTxt'])
            ->getMock();
        $repo->method('findById')->willReturn($persona);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $msg = PersonaUpdate::execute([
            'id_nom' => 1,
            'obj_pau' => 'PersonaN',
            'situacion' => 'A',
            'apellido1' => 'X',
        ]);

        $this->assertStringContainsString('db', $msg);
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
