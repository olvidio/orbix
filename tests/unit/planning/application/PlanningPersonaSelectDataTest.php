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
use src\personas\domain\entity\PersonaDl;
use src\planning\application\PlanningPersonaRepositoryPicker;
use src\planning\application\PlanningPersonaSelectData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;

final class PlanningPersonaSelectDataTest extends TestCase
{
    public function test_mapea_filas_del_repositorio(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn(100);
        $p->method('getId_tabla')->willReturn('n');
        $p->method('getPrefApellidosNombre')->willReturn('Apellido, Nombre');
        $p->method('getCentro_o_dl')->willReturn('Mi centro');

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->expects($this->once())->method('getPersonas')->willReturn([$p]);

        $useCase = $this->createUseCase(personaN: $repo);
        $out = $useCase->execute([
            'obj_pau' => 'PersonaN',
            'apellido1' => '',
            'centro' => '',
        ]);

        $this->assertCount(1, $out);
        $this->assertSame(100, $out[0]['id_nom']);
        $this->assertSame('n', $out[0]['id_tabla']);
        $this->assertSame('Apellido, Nombre', $out[0]['pref_apellidos_nombre']);
        $this->assertSame('Mi centro', $out[0]['centro_o_dl']);
    }

    public function test_con_centro_usa_repositorio_de_obj_pau(): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_nom')->willReturn(200);
        $p->method('getId_tabla')->willReturn('n');
        $p->method('getPrefApellidosNombre')->willReturn('Centro, Persona');
        $p->method('getCentro_o_dl')->willReturn('Ctr X');

        $centro = $this->createMock(CentroDl::class);
        $centro->method('getId_ubi')->willReturn(42);

        $repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $repoN->expects($this->once())
            ->method('getPersonas')
            ->with($this->callback(static fn (array $where): bool => ($where['id_ctr'] ?? null) === 42))
            ->willReturn([$p]);

        $repoDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $repoDl->expects($this->never())->method('getPersonas');

        $repoCentro = $this->createMock(CentroDlRepositoryInterface::class);
        $repoCentro->method('getCentros')->willReturn([$centro]);

        $useCase = $this->createUseCase(personaDl: $repoDl, personaN: $repoN, centroDl: $repoCentro);
        $out = $useCase->execute([
            'obj_pau' => 'PersonaN',
            'centro' => 'Mi centro',
        ]);

        $this->assertCount(1, $out);
        $this->assertSame(200, $out[0]['id_nom']);
    }

    public function test_lista_vacia(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('getPersonas')->willReturn([]);

        $useCase = $this->createUseCase(personaN: $repo);
        $this->assertSame([], $useCase->execute(['obj_pau' => 'PersonaN']));
    }

    private function createUseCase(
        ?PersonaDlRepositoryInterface $personaDl = null,
        ?PersonaNRepositoryInterface $personaN = null,
        ?CentroDlRepositoryInterface $centroDl = null,
    ): PlanningPersonaSelectData {
        $picker = new PlanningPersonaRepositoryPicker(
            $personaDl ?? $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            new PersonaRepositoryResolver(
                $personaN ?? $this->createMock(PersonaNRepositoryInterface::class),
                $this->createMock(PersonaAgdRepositoryInterface::class),
                $this->createMock(PersonaNaxRepositoryInterface::class),
                $this->createMock(PersonaSRepositoryInterface::class),
                $this->createMock(PersonaSSSCRepositoryInterface::class),
                $this->createMock(PersonaExRepositoryInterface::class),
            ),
        );

        return new PlanningPersonaSelectData(
            $picker,
            $centroDl ?? $this->createMock(CentroDlRepositoryInterface::class),
        );
    }
}
