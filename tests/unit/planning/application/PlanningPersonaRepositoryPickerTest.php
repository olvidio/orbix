<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\planning\application\PlanningPersonaRepositoryPicker;

final class PlanningPersonaRepositoryPickerTest extends TestCase
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

    public function test_get_vacio_devuelve_persona_dl(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaDlRepositoryInterface::class => $dl,
        ]);

        $picker = new PlanningPersonaRepositoryPicker();
        $this->assertSame($dl, $picker->get(''));
    }

    public function test_get_persona_dl_explicito(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaDlRepositoryInterface::class => $dl,
        ]);

        $picker = new PlanningPersonaRepositoryPicker();
        $this->assertSame($dl, $picker->get('PersonaDl'));
    }

    public function test_get_persona_n_desde_trait(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $n = $this->createMock(PersonaNRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaDlRepositoryInterface::class => $dl,
            PersonaNRepositoryInterface::class => $n,
        ]);

        $picker = new PlanningPersonaRepositoryPicker();
        $this->assertSame($n, $picker->get('PersonaN'));
    }

    public function test_get_persona_sacd(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $sacd = $this->createMock(PersonaSacdRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaDlRepositoryInterface::class => $dl,
            PersonaSacdRepositoryInterface::class => $sacd,
        ]);

        $picker = new PlanningPersonaRepositoryPicker();
        $this->assertSame($sacd, $picker->get('PersonaSacd'));
    }

    public function test_get_safe_obj_pau_invalido_caen_en_persona_dl(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $GLOBALS['container'] = $this->containerFromMap([
            PersonaDlRepositoryInterface::class => $dl,
        ]);

        $picker = new PlanningPersonaRepositoryPicker();
        $this->assertSame($dl, $picker->getSafe('PersonaDesconocida'));
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
