<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\ExpedienteNotasPersona;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\application\Tesera;
use src\notas\application\TesseraCopiar;
use src\notas\application\TesseraCopiarSelectData;
use src\notas\application\TesseraImprimirData;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaN;
use src\shared\domain\criteria\Criteria;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Traslado de tessera entre personas: elección del destino, copia de notas e impresión. */
final class TesseraCopiarTest extends myTest
{
    private PersonaNotaRepositoryInterface&MockObject $notas;
    private PersonaNotaDlRepositoryInterface&MockObject $notasDl;
    private DbSchemaRepositoryInterface&MockObject $schemas;

    public function setUp(): void
    {
        parent::setUp();
        $this->notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $this->notasDl = $this->createMock(PersonaNotaDlRepositoryInterface::class);
        $this->schemas = $this->createMock(DbSchemaRepositoryInterface::class);
    }

    private function copiar(): TesseraCopiar
    {
        return new TesseraCopiar($this->notas, $this->notasDl, $this->schemas);
    }

    private function nota(int $idSchema, int $idNivel = 1101): PersonaNota&MockObject
    {
        $n = $this->createMock(PersonaNota::class);
        $n->method('getId_schema')->willReturn($idSchema);
        $n->method('getId_nivel')->willReturn($idNivel);

        return $n;
    }

    // ---- TesseraCopiar ----------------------------------------------------------------------

    /**
     * @dataProvider entradasSinPersonasProvider
     * @param array<string, mixed> $input
     */
    public function test_sin_origen_o_destino_no_se_copia_nada(array $input): void
    {
        $this->notas->expects($this->never())->method('matching');
        $this->notasDl->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $this->copiar()->execute($input));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function entradasSinPersonasProvider(): iterable
    {
        yield 'vacía' => [[]];
        yield 'sin destino' => [['id_nom_org' => '10011']];
        yield 'sin origen' => [['id_nom_dst' => '10012']];
        yield 'ceros' => [['id_nom_org' => '0', 'id_nom_dst' => '0']];
    }

    public function test_busca_las_notas_de_la_persona_origen(): void
    {
        $this->notas->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_nom eq 10011'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->assertSame('', $this->copiar()->execute(['id_nom_org' => '10011', 'id_nom_dst' => '10012']));
    }

    public function test_copia_las_notas_sin_esquema_a_la_dl_de_sesion_con_el_id_del_destino(): void
    {
        $origen = $this->nota(0);
        $this->notas->method('matching')->willReturn([$origen, $this->nota(0, 1102)]);

        $guardadas = [];
        $this->notasDl->expects($this->exactly(2))->method('Guardar')->willReturnCallback(function (PersonaNota $n) use (&$guardadas): bool {
            $guardadas[] = $n;

            return true;
        });

        $this->assertSame('', $this->copiar()->execute(['id_nom_org' => '10011', 'id_nom_dst' => '10012']));
        $this->assertNotSame($origen, $guardadas[0], 'se guarda una copia, no el original');
    }

    public function test_un_esquema_desconocido_usa_la_dl_de_sesion(): void
    {
        $this->notas->method('matching')->willReturn([$this->nota(77)]);
        $this->schemas->method('matching')->willReturn([]);
        $this->notasDl->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame('', $this->copiar()->execute(['id_nom_org' => '10011', 'id_nom_dst' => '10012']));
    }

    /**
     * @dataProvider esquemasSinEscrituraProvider
     */
    public function test_restov_y_restof_no_se_pueden_escribir(string $esquema): void
    {
        $this->notas->method('matching')->willReturn([$this->nota(77, 1203)]);
        $s = new DbSchema();
        $s->setSchema($esquema);
        $this->schemas->method('matching')->willReturn([$s]);
        $this->notasDl->expects($this->never())->method('Guardar');

        $error = $this->copiar()->execute(['id_nom_org' => '10011', 'id_nom_dst' => '10012']);

        $this->assertStringStartsWith('<br>', $error);
        $this->assertStringContainsString('1203', $error);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function esquemasSinEscrituraProvider(): iterable
    {
        yield 'restov' => ['restov'];
        yield 'restof' => ['restof'];
    }

    public function test_cada_fallo_de_guardado_suma_un_error_y_sigue(): void
    {
        $this->notas->method('matching')->willReturn([$this->nota(0), $this->nota(0), $this->nota(0)]);
        $this->notasDl->method('Guardar')->willReturnOnConsecutiveCalls(false, true, false);

        $error = $this->copiar()->execute(['id_nom_org' => '10011', 'id_nom_dst' => '10012']);

        $this->assertSame(2, substr_count($error, '<br>'));
    }

    // ---- TesseraCopiarSelectData ----------------------------------------------------------------------

    private function personaN(int $id, string $nombre, string $ap1 = 'Gil'): PersonaN&MockObject
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getApellido1')->willReturn($ap1);
        $p->method('getNombreApellidos')->willReturn($nombre);

        return $p;
    }

    private function personaAgd(int $id, string $nombre, string $ap1 = 'Gil'): PersonaAgd&MockObject
    {
        $p = $this->createMock(PersonaAgd::class);
        $p->method('getId_nom')->willReturn($id);
        $p->method('getApellido1')->willReturn($ap1);
        $p->method('getNombreApellidos')->willReturn($nombre);

        return $p;
    }

    public function test_sin_la_persona_origen_lanza_excepcion(): void
    {
        $n = $this->createMock(PersonaNRepositoryInterface::class);
        $agd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $n->method('findById')->willReturn(null);
        $agd->method('findById')->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('10011');
        (new TesseraCopiarSelectData($n, $agd))->execute(10011);
    }

    public function test_los_posibles_destinos_comparten_apellido_con_sufijo_de_tipo_y_sin_el_origen(): void
    {
        $n = $this->createMock(PersonaNRepositoryInterface::class);
        $agd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $n->method('findById')->willReturn($this->personaN(10011, 'Ana Gil'));
        $agd->expects($this->never())->method('findById');
        $criterios = [];
        $n->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);

            return [$this->personaN(10011, 'Ana Gil'), $this->personaN(10020, 'Zoe Gil'), $this->personaN(10021, 'Ben Gil')];
        });
        $agd->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);

            return [$this->personaAgd(10030, 'Carla Gil')];
        });

        $r = (new TesseraCopiarSelectData($n, $agd))->execute(10011);

        $this->assertSame('Ana Gil', $r['nom']);
        $this->assertSame([10021 => 'Ben Gil (n)', 10030 => 'Carla Gil (agd)', 10020 => 'Zoe Gil (n)'], $r['posibles_personas']);
        $this->assertSame([['apellido1 eq Gil'], ['apellido1 eq Gil']], $criterios);
    }

    public function test_si_el_origen_es_agregado_se_busca_en_agd(): void
    {
        $n = $this->createMock(PersonaNRepositoryInterface::class);
        $agd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $n->method('findById')->willReturn(null);
        $n->method('matching')->willReturn([]);
        $agd->method('findById')->with(10030)->willReturn($this->personaAgd(10030, 'Carla Gil'));
        $agd->method('matching')->willReturn([$this->personaAgd(10030, 'Carla Gil')]);

        $r = (new TesseraCopiarSelectData($n, $agd))->execute(10030);

        $this->assertSame('Carla Gil', $r['nom']);
        $this->assertSame([], $r['posibles_personas']);
    }

    // ---- TesseraImprimirData ----------------------------------------------------------------------

    public function test_imprimir_sin_persona_devuelve_solo_aviso(): void
    {
        $this->notas->expects($this->never())->method('matching');
        $tesera = new Tesera(
            new ExpedienteNotasPersona($this->notas),
            $this->createMock(AsignaturaRepositoryInterface::class),
            new PlanEstudiosDePersona($this->notas)
        );

        $r = (new TesseraImprimirData($tesera, $this->createStub(\src\personas\application\services\PersonaFinderService::class)))->execute(0);

        $this->assertSame(['aviso'], array_keys($r));
        $this->assertNotSame('', $r['aviso']);
    }
}
