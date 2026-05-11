<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaNRepositoryInterface;

final class PersonaRepositoryResolverTest extends TestCase
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

    public function test_repositorio_pide_al_contenedor(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $resolver = new PersonaRepositoryResolver();
        $this->assertSame($repo, $resolver->repositorio('PersonaN'));
    }

    public function test_repositorioPorIdTabla(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaNRepositoryInterface::class => $repo,
        ]);

        $resolver = new PersonaRepositoryResolver();
        $this->assertSame($repo, $resolver->repositorioPorIdTabla('n'));
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
