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
use src\personas\domain\entity\PersonaN;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\value_objects\DelegacionCode;
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

    public function test_edicion_persona_ex_opciones_dl_solo_sin_esquema(): void
    {
        $out = $this->editarPersonaExConDl('xyz');

        $this->assertSame(['xyz' => 'xyz', 'abc' => 'abc'], $out['opciones_dl']);
    }

    public function test_alta_persona_ex_opciones_dl_solo_sin_esquema(): void
    {
        $repo = $this->createMock(PersonaExRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(7);
        $repo->method('getNewIdNom')->with(7)->willReturn(-7);

        $useCase = $this->makeUseCase(
            $this->makeResolver([PersonaExRepositoryInterface::class => $repo]),
            $this->delegacionesActivas(),
            $this->dbPropiedadesConEsquema(['bcn' => 'bcn']),
        );

        $out = $useCase->execute([
            'nuevo' => 1,
            'obj_pau' => 'PersonaEx',
            'id_tabla' => 'pn',
        ]);

        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame(['xyz' => 'xyz', 'abc' => 'abc'], $out['opciones_dl']);
    }

    public function test_edicion_persona_ex_conserva_dl_actual_aunque_tenga_esquema(): void
    {
        $out = $this->editarPersonaExConDl('bcn');

        $this->assertSame(
            ['xyz' => 'xyz', 'abc' => 'abc', 'bcn' => 'bcn'],
            $out['opciones_dl'],
        );
    }

    public function test_edicion_persona_n_opciones_dl_incluye_las_con_esquema(): void
    {
        $persona = new PersonaN();
        $persona->setId_nom(21);
        $persona->setId_tabla('n');
        $persona->setApellido1('Garcia');
        $persona->setSituacion('A');
        $persona->setDlVo(new DelegacionCode('bcn'));

        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->with(21)->willReturn($persona);

        $useCase = $this->makeUseCase(
            $this->makeResolver([PersonaNRepositoryInterface::class => $repo]),
            $this->delegacionesActivas(),
            $this->dbPropiedadesConEsquema(['bcn' => 'bcn']),
        );

        $out = $useCase->execute([
            'nuevo' => 0,
            'obj_pau' => 'PersonaN',
            'id_nom' => 21,
        ]);

        $this->assertArrayNotHasKey('error', $out);
        $this->assertSame(
            ['bcn' => 'bcn', 'xyz' => 'xyz', 'abc' => 'abc'],
            $out['opciones_dl'],
        );
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

        return $this->ejecutarEdicionPersonaEx($persona);
    }

    /**
     * @return array<string, mixed>
     */
    private function editarPersonaExConDl(string $dl): array
    {
        $persona = new PersonaEx();
        $persona->setId_nom(21);
        $persona->setId_tabla('pn');
        $persona->setApellido1('Garcia');
        $persona->setSituacion('A');
        $persona->setDlVo(new DelegacionCode($dl));

        return $this->ejecutarEdicionPersonaEx(
            $persona,
            $this->delegacionesActivas(),
            $this->dbPropiedadesConEsquema(['bcn' => 'bcn']),
        );
    }

    /**
     * @param list<Delegacion> $delegaciones
     * @return array<string, mixed>
     */
    private function ejecutarEdicionPersonaEx(
        PersonaEx $persona,
        array $delegaciones = [],
        ?DBPropiedades $dbPropiedades = null,
    ): array {
        $repo = $this->createMock(PersonaExRepositoryInterface::class);
        $repo->method('findById')->with(21)->willReturn($persona);

        $useCase = $this->makeUseCase(
            $this->makeResolver([PersonaExRepositoryInterface::class => $repo]),
            $delegaciones,
            $dbPropiedades,
        );

        $out = $useCase->execute([
            'nuevo' => 0,
            'obj_pau' => 'PersonaEx',
            'id_nom' => 21,
        ]);

        $this->assertArrayNotHasKey('error', $out);

        return $out;
    }

    /**
     * @param list<Delegacion> $delegaciones
     */
    private function makeUseCase(
        PersonaRepositoryResolver $resolver,
        array $delegaciones = [],
        ?DBPropiedades $dbPropiedades = null,
    ): PersonasEditarData {
        $delegacion = $this->createMock(DelegacionRepositoryInterface::class);
        $delegacion->method('getDelegaciones')->willReturn($delegaciones);

        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getArrayCentros')->willReturn([]);

        $situacion = $this->createMock(SituacionRepositoryInterface::class);
        $situacion->method('getArraySituaciones')->willReturn([]);

        $local = $this->createMock(LocalRepositoryInterface::class);
        $local->method('getArrayLocales')->willReturn([]);

        if ($dbPropiedades === null) {
            $dbPropiedades = $this->createMock(DBPropiedades::class);
            $dbPropiedades->method('array_posibles_dl_de_esquemas')->willReturn([]);
        }

        return new PersonasEditarData(
            $resolver,
            $centroDl,
            $this->createMock(CentroRepositoryInterface::class),
            $delegacion,
            $situacion,
            $local,
            $dbPropiedades,
        );
    }

    /**
     * @return list<Delegacion>
     */
    private function delegacionesActivas(): array
    {
        return [
            $this->makeDelegacion('bcn'),
            $this->makeDelegacion('xyz'),
            $this->makeDelegacion('abc'),
        ];
    }

    /**
     * @param array<string, string> $conEsquema
     */
    private function dbPropiedadesConEsquema(array $conEsquema): DBPropiedades
    {
        $dbPropiedades = $this->createMock(DBPropiedades::class);
        $dbPropiedades->method('array_posibles_dl_de_esquemas')->with(true)->willReturn($conEsquema);

        return $dbPropiedades;
    }

    private function makeDelegacion(string $dl): Delegacion
    {
        $delegacion = $this->createStub(Delegacion::class);
        $delegacion->method('getDlVo')->willReturn(new DelegacionCode($dl));

        return $delegacion;
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
