<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\application\PersonaUpdate;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\infrastructure\persistence\postgresql\PgPersonaNRepository;

final class PersonaUpdateTest extends TestCase
{
    public function test_sin_id_nom(): void
    {
        $useCase = new PersonaUpdate($this->makeResolver());

        $this->assertNotSame('', $useCase->execute(['id_nom' => 0, 'obj_pau' => 'PersonaN']));
    }

    public function test_obj_pau_desconocido(): void
    {
        $useCase = new PersonaUpdate($this->makeResolver());

        $this->assertNotSame('', $useCase->execute(['id_nom' => 1, 'obj_pau' => 'PersonaX']));
    }

    public function test_exito_actualiza_mock(): void
    {
        $persona = $this->createMock(PersonaN::class);

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(50)->willReturn($persona);
        $repo->expects($this->once())->method('Guardar')->with($persona)->willReturn(true);

        $useCase = new PersonaUpdate($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ]));

        $input = [
            'id_nom' => 50,
            'obj_pau' => 'PersonaN',
            'situacion' => 'A',
            'nom' => 'Ana',
            'apel_fam' => 'G',
            'nx1' => '',
            'apellido1' => 'P',
            'nx2' => '',
            'apellido2' => '',
        ];

        $this->assertSame('', $useCase->execute($input));
    }

    public function test_exito_persona_ex_sin_id_ctr_ni_ce(): void
    {
        $persona = new PersonaEx();
        $persona->setId_nom(99);
        $persona->setId_tabla('pn');
        $persona->setApellido1('Externa');
        $persona->setSituacion('A');

        $repo = $this->createMock(PersonaExRepositoryInterface::class);
        $repo->method('findById')->with(99)->willReturn($persona);
        $repo->expects($this->once())->method('Guardar')->with($persona)->willReturn(true);

        $useCase = new PersonaUpdate($this->makeResolver([
            PersonaExRepositoryInterface::class => $repo,
        ]));

        $result = $useCase->execute([
            'id_nom' => 99,
            'obj_pau' => 'PersonaEx',
            'dl' => 'BCN',
            'id_ctr' => 123,
            'nivel_stgr' => NivelStgrId::N,
            'situacion' => 'A',
            'apellido1' => 'Externa',
            'edad' => '42',
            'ce' => 1,
            'ce_lugar' => 'x',
            'ce_ini' => 2,
            'ce_fin' => 3,
        ]);
        $this->assertSame('', $result);
        $this->assertSame(42, $persona->getEdad());
    }

    public function test_falla_guardar(): void
    {
        $persona = $this->createMock(PersonaN::class);

        $repo = $this->getMockBuilder(PgPersonaNRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findById', 'Guardar', 'getErrorTxt'])
            ->getMock();
        $repo->method('findById')->willReturn($persona);
        $repo->method('Guardar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('db');

        $msg = (new PersonaUpdate($this->makeResolver([
            PersonaNRepositoryInterface::class => $repo,
        ])))->execute([
            'id_nom' => 1,
            'obj_pau' => 'PersonaN',
            'situacion' => 'A',
            'apellido1' => 'X',
        ]);

        $this->assertStringContainsString('db', $msg);
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
