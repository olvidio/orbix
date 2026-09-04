<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\TestCase;
use src\personas\application\PersonasEditarData;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;

final class PersonasEditarDataTest extends TestCase
{
    public function test_edicion_persona_ex_pa_no_fuerza_numerario(): void
    {
        $out = $this->editarPersonaExConIdTabla('pa');

        $this->assertSame('pa', $out['id_tabla']);
    }

    public function test_edicion_persona_ex_sssc_sale_como_psssc(): void
    {
        $out = $this->editarPersonaExConIdTabla('sssc');

        $this->assertSame('psssc', $out['id_tabla']);
    }

    public function test_edicion_persona_ex_psss_sale_como_psssc(): void
    {
        $out = $this->editarPersonaExConIdTabla('psss');

        $this->assertSame('psssc', $out['id_tabla']);
    }

    /**
     * @return array<string, mixed>
     */
    private function editarPersonaExConIdTabla(string $id_tabla): array
    {
        $persona = new PersonaEx();
        $persona->setId_nom(21);
        $persona->setId_tabla($id_tabla);
        $persona->setApellido1('Garcia');
        $persona->setSituacion('A');

        $repo = $this->createMock(PersonaExRepositoryInterface::class);
        $repo->method('findById')->with(21)->willReturn($persona);

        $useCase = $this->makeUseCase($this->makeResolver([
            PersonaExRepositoryInterface::class => $repo,
        ]));

        $out = $useCase->execute([
            'nuevo' => 0,
            'obj_pau' => 'PersonaEx',
            'id_nom' => 21,
        ]);

        $this->assertArrayNotHasKey('error', $out);

        return $out;
    }

    private function makeUseCase(PersonaRepositoryResolver $resolver): PersonasEditarData
    {
        $delegacion = $this->createMock(DelegacionRepositoryInterface::class);
        $delegacion->method('getDelegaciones')->willReturn([]);

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([]);

        $situacion = $this->createMock(SituacionRepositoryInterface::class);
        $situacion->method('getArraySituaciones')->willReturn([]);

        $local = $this->createMock(LocalRepositoryInterface::class);
        $local->method('getArrayLocales')->willReturn([]);

        return new PersonasEditarData(
            $resolver,
            $centroDl,
            $this->createMock(CentroRepositoryInterface::class),
            $delegacion,
            $situacion,
            $local,
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
