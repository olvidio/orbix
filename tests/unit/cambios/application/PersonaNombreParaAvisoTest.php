<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use src\cambios\application\PersonaNombreParaAviso;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaSacd;
use src\shared\config\ServerConf;

/**
 * Unitarios para {@see PersonaNombreParaAviso}.
 *
 * Los sacd de paso tienen `id_nom` negativo y viven en `p_de_paso_ex`;
 * el resolver debe consultarlos vía {@see PersonaFinderService::findPersonaEnGlobalODePaso()}.
 */
final class PersonaNombreParaAvisoTest extends TestCase
{
    private mixed $previousContainer;
    private bool $previousDmz;
    /** @var array<string, mixed> */
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousDmz = ServerConf::$dmz;
        $this->previousSession = $_SESSION ?? [];
        ServerConf::$dmz = false;
        unset($_SESSION['private']);
    }

    protected function tearDown(): void
    {
        ServerConf::$dmz = $this->previousDmz;
        $_SESSION = $this->previousSession;
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    public function test_id_cero_devuelve_null_sin_consultar(): void
    {
        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->expects($this->never())->method('findById');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->never())->method('findPersonaEnGlobalODePaso');
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $resolver = new PersonaNombreParaAviso($sacdRepo);

        $this->assertNull($resolver->resolve(0));
    }

    public function test_sacd_de_paso_usa_find_persona_en_global_o_de_paso(): void
    {
        $idDePaso = -10016280;
        $personaEx = $this->createStub(PersonaEx::class);
        $personaEx->method('getPrefApellidosNombre')->willReturn('Martínez, Pedro');

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->once())
            ->method('findPersonaEnGlobalODePaso')
            ->with($idDePaso)
            ->willReturn($personaEx);

        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->expects($this->never())->method('findById');

        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $resolver = new PersonaNombreParaAviso($sacdRepo);

        $this->assertSame('Martínez, Pedro', $resolver->resolve($idDePaso));
    }

    public function test_en_dmz_sacd_de_paso_cae_a_cp_sacd(): void
    {
        ServerConf::$dmz = true;
        $idDePaso = -10016280;

        $personaSacd = $this->createStub(PersonaSacd::class);
        $personaSacd->method('getPrefApellidosNombre')->willReturn('López, Andrés');

        $sacdRepo = $this->createMock(PersonaSacdRepositoryInterface::class);
        $sacdRepo->expects($this->once())
            ->method('findById')
            ->with($idDePaso)
            ->willReturn($personaSacd);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->expects($this->never())->method('findPersonaEnGlobalODePaso');
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaFinderService::class => $finder,
        ]);

        $resolver = new PersonaNombreParaAviso($sacdRepo);

        $this->assertSame('López, Andrés', $resolver->resolve($idDePaso));
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
