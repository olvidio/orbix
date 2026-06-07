<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;

final class PersonaRepositoryResolverTest extends TestCase
{
    public function test_idTablaFor_persona_n(): void
    {
        $this->assertSame('n', PersonaRepositoryResolver::idTablaFor('PersonaN'));
    }

    public function test_idTablaFor_desconocido_lanza(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PersonaRepositoryResolver::idTablaFor('PersonaFoo');
    }

    public function test_entityTypeByIdTabla_incluye_alias_cp_sss(): void
    {
        $map = PersonaRepositoryResolver::entityTypeByIdTabla();
        $this->assertSame('PersonaSSSC', $map['cp_sss']);
    }

    public function test_repositorio_devuelve_repo_inyectado(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $resolver = $this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertSame($repo, $resolver->repositorio('PersonaN'));
    }

    public function test_repositorioPorIdTabla(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $resolver = $this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $this->assertSame($repo, $resolver->repositorioPorIdTabla('n'));
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
