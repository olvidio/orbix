<?php

declare(strict_types=1);

namespace Tests\unit\personas\domain\entity;

use DI\Container;
use DI\ContainerBuilder;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\Persona;
use src\personas\domain\entity\PersonaDl;
use Tests\myTest;

final class PersonaTest extends myTest
{
    private mixed $previousContainer;

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_findPersonaEnGlobal_delegates_to_service(): void
    {
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $persona = new PersonaDl();
        $problemas = [];

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobal')
            ->with(42, $this->isArray())
            ->willReturn($persona);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $result = Persona::findPersonaEnGlobal(42, $problemas);
        $this->assertSame($persona, $result);
    }

    public function test_buscarEnTodasRegiones_delegates_to_service(): void
    {
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $expected = [['esquema' => 'H-dlbv', 'persona' => new PersonaDl()]];

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('buscarEnTodasRegiones')
            ->with(99)
            ->willReturn($expected);

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $result = Persona::buscarEnTodasRegiones(99);
        $this->assertSame($expected, $result);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): Container
    {
        $builder = new ContainerBuilder();
        $definitions = [];
        foreach ($services as $id => $service) {
            $definitions[$id] = static fn (): object => $service;
        }
        $builder->addDefinitions($definitions);

        return $builder->build();
    }
}
