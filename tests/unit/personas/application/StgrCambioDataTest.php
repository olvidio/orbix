<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\StgrCambioData;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaN;

final class StgrCambioDataTest extends TestCase
{
    public function test_id_tabla_vacio(): void
    {
        $useCase = new StgrCambioData($this->makeResolver());

        $out = $useCase->execute(['id_nom' => 1, 'id_tabla' => '']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_id_tabla_desconocido(): void
    {
        $useCase = new StgrCambioData($this->makeResolver());

        $out = $useCase->execute(['id_nom' => 1, 'id_tabla' => 'zzz']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $useCase = new StgrCambioData($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $out = $useCase->execute(['id_nom' => 99, 'id_tabla' => 'n']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_exito(): void
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getNombreApellidos')->willReturn('Nom Ap');
        $p->method('getNivel_stgr')->willReturn(2);

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(5)->willReturn($p);

        $useCase = new StgrCambioData($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $out = $useCase->execute(['id_nom' => 5, 'id_tabla' => 'n']);
        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame('Nom Ap', $out['nom']);
        $this->assertSame('2', $out['nivel_stgr']);
        $this->assertSame(5, $out['id_nom']);
        $this->assertSame('n', $out['id_tabla']);
        $this->assertIsArray($out['opciones_nivel_stgr']);
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
        );
    }
}
