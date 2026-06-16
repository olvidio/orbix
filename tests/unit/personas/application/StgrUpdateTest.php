<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\StgrUpdate;
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
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;

final class StgrUpdateTest extends TestCase
{
    public function test_id_tabla_desconocido(): void
    {
        $useCase = new StgrUpdate($this->makeResolver());

        $this->assertNotSame('', $useCase->execute(1, 'bad', 1));
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new StgrUpdate($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $this->assertNotSame('', $useCase->execute(2, 'n', 1));
    }

    public function test_exito(): void
    {
        $p = $this->createMock(PersonaN::class);
        $p->expects($this->once())->method('setNivel_stgr')->with(3);

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn($p);
        $repo->expects($this->once())->method('Guardar')->with($p)->willReturn(true);

        $useCase = new StgrUpdate($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $this->assertSame('', $useCase->execute(1, 'n', 3));
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

        $useCase = new StgrUpdate($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $this->assertStringContainsString('stgr-db', $useCase->execute(1, 'n', 0));
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
