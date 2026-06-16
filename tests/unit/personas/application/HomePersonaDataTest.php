<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\HomePersonaData;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\services\TelecoPersonaService;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class HomePersonaDataTest extends TestCase
{
    public function test_obj_pau_desconocido(): void
    {
        $useCase = $this->makeUseCase($this->makeResolver());

        $out = $useCase->execute(['id_nom' => 1, 'obj_pau' => 'PersonaZ']);
        $this->assertArrayHasKey('error', $out);
    }

    public function test_persona_no_encontrada(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);

        $useCase = $this->makeUseCase($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $out = $useCase->execute(['id_nom' => 8, 'obj_pau' => 'PersonaN']);
        $this->assertArrayHasKey('error', $out);
    }

    private function makeUseCase(PersonaRepositoryResolver $resolver): HomePersonaData
    {
        return new HomePersonaData(
            $resolver,
            $this->createMock(PersonaPubRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(TelecoPersonaService::class),
        );
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
