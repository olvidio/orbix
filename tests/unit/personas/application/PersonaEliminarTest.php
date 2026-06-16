<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaEliminar;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaN;

final class PersonaEliminarTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_sin_id_nom(): void
    {
        $useCase = new PersonaEliminar($this->makeResolver());

        $this->assertNotSame('', $useCase->execute(0, 'PersonaN'));
    }

    public function test_obj_pau_desconocido(): void
    {
        $useCase = new PersonaEliminar($this->makeResolver());

        $this->assertNotSame('', $useCase->execute(1, 'PersonaX'));
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn(null);

        $useCase = new PersonaEliminar($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $this->assertNotSame('', $useCase->execute(9, 'PersonaN'));
    }

    public function test_exito_cuando_dl_coincide(): void
    {
        $persona = $this->createMock(PersonaN::class);
        $persona->method('getDl')->willReturn('dlb');

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn($persona);
        $repo->expects($this->once())->method('Eliminar')->with($persona)->willReturn(true);

        $_SESSION['session_auth'] = array_merge($_SESSION['session_auth'] ?? [], [
            'esquema' => 'R-dlbv',
            'sfsv' => 1,
        ]);

        $useCase = new PersonaEliminar($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $this->assertSame('', $useCase->execute(3, 'PersonaN'));
    }

    /**
     * @param array<class-string, object> $overrides
     */
    private function makeResolver(array $overrides = []): PersonaRepositoryResolver
    {
        return new PersonaRepositoryResolver(
            $overrides[PersonaNRepositoryInterface::class] ?? $this->createMock(PersonaNRepositoryInterface::class),
            $overrides[PersonaAgdRepositoryInterface::class] ?? $this->createMock(PersonaAgdRepositoryInterface::class),
            $overrides[PersonaNaxRepositoryInterface::class] ?? $this->createMock(PersonaNaxRepositoryInterface::class),
            $overrides[PersonaSRepositoryInterface::class] ?? $this->createMock(PersonaSRepositoryInterface::class),
            $overrides[PersonaSSSCRepositoryInterface::class] ?? $this->createMock(PersonaSSSCRepositoryInterface::class),
            $overrides[PersonaExRepositoryInterface::class] ?? $this->createMock(PersonaExRepositoryInterface::class),
            $overrides[PersonaDlRepositoryInterface::class] ?? $this->createMock(PersonaDlRepositoryInterface::class),
            $overrides[PersonaSacdRepositoryInterface::class] ?? $this->createMock(PersonaSacdRepositoryInterface::class),
        );
    }
}
