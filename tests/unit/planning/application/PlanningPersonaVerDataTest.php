<?php

declare(strict_types=1);

namespace Tests\unit\planning\application;

use PHPUnit\Framework\MockObject\MockObject;
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
use src\personas\domain\entity\PersonaDl;
use src\planning\application\ActividadesDePersonaService;
use src\planning\application\PlanningPersonaRepositoryPicker;
use src\planning\application\PlanningPersonaVerData;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\support\DescribeCriteria;

final class PlanningPersonaVerDataTest extends TestCase
{
    private PersonaDlRepositoryInterface&MockObject $personaDl;
    private PersonaNRepositoryInterface&MockObject $personaN;
    private ActividadesDePersonaService&MockObject $actividades;
    private PlanningPersonaVerData $useCase;
    private DateTimeLocal $ini;

    protected function setUp(): void
    {
        $this->personaDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->personaN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadesDePersonaService::class);
        $picker = new PlanningPersonaRepositoryPicker(
            $this->personaDl,
            $this->createMock(PersonaSacdRepositoryInterface::class),
            new PersonaRepositoryResolver(
                $this->personaN,
                $this->createMock(PersonaAgdRepositoryInterface::class),
                $this->createMock(PersonaNaxRepositoryInterface::class),
                $this->createMock(PersonaSRepositoryInterface::class),
                $this->createMock(PersonaSSSCRepositoryInterface::class),
                $this->createMock(PersonaExRepositoryInterface::class),
                $this->personaDl,
                $this->createMock(PersonaSacdRepositoryInterface::class),
            ),
        );
        $this->useCase = new PlanningPersonaVerData($picker, $this->actividades);
        $this->ini = new DateTimeLocal('2025-01-01');
    }

    public function test_busca_las_personas_por_id_nom_como_enteros(): void
    {
        $this->personaDl->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_nom in [100,200,300]'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->useCase->execute([], ['100', '200', '300'], $this->ini, '1/1/2025', '2025-03-31', '2025-01-01');
    }

    public function test_pasa_las_personas_al_servicio_sin_agrupar_por_centro(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $this->personaDl->method('matching')->willReturn([$p]);
        $this->actividades->expects($this->once())->method('actividadesPorPersona')
            ->with([$p], '2025-03-31', '2025-01-01', $this->ini, '1/1/2025', false)
            ->willReturn([['persona' => 'x']]);

        $out = $this->useCase->execute([], ['100'], $this->ini, '1/1/2025', '2025-03-31', '2025-01-01');

        $this->assertSame(['a_actividades' => [['persona' => 'x']]], $out);
    }

    public function test_sin_obj_pau_o_con_persona_dl_usa_el_repositorio_de_dl(): void
    {
        $this->personaDl->expects($this->exactly(2))->method('matching')->willReturn([]);
        $this->personaN->expects($this->never())->method('matching');

        $this->useCase->execute([], ['1'], $this->ini, '', '', '');
        $this->useCase->execute(['obj_pau' => 'PersonaDl'], ['1'], $this->ini, '', '', '');
    }

    public function test_con_obj_pau_usa_el_repositorio_de_ese_tipo(): void
    {
        $this->personaN->expects($this->once())->method('matching')->willReturn([]);
        $this->personaDl->expects($this->never())->method('matching');

        $this->useCase->execute(['obj_pau' => 'PersonaN'], ['1'], $this->ini, '', '', '');
    }

    public function test_un_obj_pau_desconocido_cae_al_repositorio_de_dl(): void
    {
        $this->personaDl->expects($this->once())->method('matching')->willReturn([]);

        $out = $this->useCase->execute(['obj_pau' => 'NoExiste'], ['1'], $this->ini, '', '', '');

        $this->assertSame(['a_actividades' => []], $out);
    }

    public function test_sin_personas_seleccionadas_no_falla(): void
    {
        $this->personaDl->method('matching')->willReturn([]);
        $this->actividades->method('actividadesPorPersona')->willReturn([]);

        $this->assertSame(['a_actividades' => []], $this->useCase->execute([], [], $this->ini, '', '', ''));
    }
}
