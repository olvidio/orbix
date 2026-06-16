<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\planning\application\PlanningPersonaRepositoryPicker;

final class PlanningPersonaRepositoryPickerTest extends TestCase
{
    public function test_get_vacio_devuelve_persona_dl(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $picker = $this->createPicker($dl);

        $this->assertSame($dl, $picker->get(''));
    }

    public function test_get_persona_dl_explicito(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $picker = $this->createPicker($dl);

        $this->assertSame($dl, $picker->get('PersonaDl'));
    }

    public function test_get_persona_n_desde_resolver(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $n = $this->createMock(PersonaNRepositoryInterface::class);
        $picker = $this->createPicker($dl, personaN: $n);

        $this->assertSame($n, $picker->get('PersonaN'));
    }

    public function test_get_persona_sacd(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $sacd = $this->createMock(PersonaSacdRepositoryInterface::class);
        $picker = $this->createPicker($dl, personaSacd: $sacd);

        $this->assertSame($sacd, $picker->get('PersonaSacd'));
    }

    public function test_get_safe_obj_pau_invalido_caen_en_persona_dl(): void
    {
        $dl = $this->createMock(PersonaDlRepositoryInterface::class);
        $picker = $this->createPicker($dl);

        $this->assertSame($dl, $picker->getSafe('PersonaDesconocida'));
    }

    private function createPicker(
        PersonaDlRepositoryInterface $personaDl,
        ?PersonaSacdRepositoryInterface $personaSacd = null,
        ?PersonaNRepositoryInterface $personaN = null,
    ): PlanningPersonaRepositoryPicker {
        return new PlanningPersonaRepositoryPicker(
            $personaDl,
            $personaSacd ?? $this->createMock(PersonaSacdRepositoryInterface::class),
            new PersonaRepositoryResolver(
                $personaN ?? $this->createMock(PersonaNRepositoryInterface::class),
                $this->createMock(PersonaAgdRepositoryInterface::class),
                $this->createMock(PersonaNaxRepositoryInterface::class),
                $this->createMock(PersonaSRepositoryInterface::class),
                $this->createMock(PersonaSSSCRepositoryInterface::class),
                $this->createMock(PersonaExRepositoryInterface::class),
                $personaDl,
                $personaSacd ?? $this->createMock(PersonaSacdRepositoryInterface::class),
            ),
        );
    }
}
