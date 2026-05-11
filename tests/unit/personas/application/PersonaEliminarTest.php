<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaEliminar;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;

final class PersonaEliminarTest extends TestCase
{
    private mixed $previousContainer;

    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
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

        $this->assertNotSame('', PersonaEliminar::execute(0, 'PersonaN'));
    }

    public function test_obj_pau_desconocido(): void
    {
        $GLOBALS['container'] = $this->containerFromMap([]);

        $this->assertNotSame('', PersonaEliminar::execute(1, 'PersonaX'));
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertNotSame('', PersonaEliminar::execute(9, 'PersonaN'));
    }

    public function test_exito_cuando_dl_coincide(): void
    {
        $persona = $this->createMock(PersonaN::class);
        $persona->method('getDl')->willReturn('dlb');

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn($persona);
        $repo->expects($this->once())->method('Eliminar')->with($persona)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], [
            'esquema' => 'R-dlbv',
            'sfsv' => 1,
        ]);

        $this->assertSame('', PersonaEliminar::execute(3, 'PersonaN'));
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
