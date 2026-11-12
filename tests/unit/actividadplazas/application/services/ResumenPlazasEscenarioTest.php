<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application\services;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\asistentes\application\services\AsistenteActividadService;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\support\DescribeCriteria;

/**
 * Cálculo de plazas con un escenario completo. Actividad 10 de dlb, publicada,
 * 40 plazas. Calendario: dlb 20, dlp 15, dlz 5. dlb cede 3 a dlp (en su fila);
 * dlp cede 2 a dlz (en una fila de su tabla). Ocupadas (dl, propietario):
 * dlb/dlb 10, dlp/dlp 13, dlp/dlb 1, dlz/dlz 0, dlz/dlp 2.
 */
final class ResumenPlazasEscenarioTest extends TestCase
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private ActividadPlazasRepositoryInterface&MockObject $plazas;
    private AsistenteActividadService&MockObject $ocupadas;
    private ActividadAll $actividad;
    /** @var list<ActividadPlazas> */
    private array $filas = [];
    private mixed $sesion = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $this->miDl('dlb');
        $this->actividad = new ActividadAll();
        $this->actividad->setId_activ(10);
        $this->actividad->setDl_org('dlb');
        $this->actividad->setPublicado(true);
        $this->actividad->setPlazas(40);
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->actividades->method('findById')->willReturnCallback(fn (int $id): ?ActividadAll => $id === 10 ? $this->actividad : null);

        $this->filas = [
            $this->fila(1, 'dlb', 20, ['dlp' => 3]),
            $this->fila(2, 'dlb', 15),
            $this->fila(3, 'dlb', 5),
            $this->fila(2, 'dlp', null, ['dlz' => 2]),
        ];
        $this->plazas = $this->createMock(ActividadPlazasRepositoryInterface::class);
        $this->plazas->method('matching')->willReturnCallback(fn (Criteria $c): array => $this->filtrar(DescribeCriteria::filtros($c)));

        $this->ocupadas = $this->createMock(AsistenteActividadService::class);
        $this->ocupadas->method('getPlazasOcupadasPorDl')->willReturnCallback(
            fn (int $id, string $dl, string $prop = ''): int => [
                'dlb/dlb' => 10, 'dlp/dlp' => 13, 'dlp/dlb' => 1, 'dlz/dlz' => 0, 'dlz/dlp' => 2,
            ]["$dl/$prop"] ?? 0
        );
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    private function miDl(string $dl): void
    {
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-' . $dl . 'v'];
    }

    private function fila(int $idDl, string $tabla, ?int $plazas, ?array $cedidas = null): ActividadPlazas
    {
        $p = new ActividadPlazas();
        $p->setId_activ(10);
        $p->setId_dl($idDl);
        $p->setDlTablaVo($tabla);
        $p->setPlazas($plazas);
        $p->setCedidas($cedidas);
        return $p;
    }

    /** @param list<string> $filtros */
    private function filtrar(array $filtros): array
    {
        return array_values(array_filter($this->filas, function (ActividadPlazas $p) use ($filtros): bool {
            foreach ($filtros as $f) {
                [$campo, , $valor] = explode(' ', $f, 3);
                $real = match ($campo) {
                    'id_activ' => (string) $p->getId_activ(),
                    'id_dl' => (string) $p->getId_dl(),
                    'dl_tabla' => $p->getDl_tabla(),
                };
                if ($real !== $valor) {
                    return false;
                }
            }
            return true;
        }));
    }

    private function svc(): ResumenPlazasService
    {
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->method('matching')->willReturn(array_map(function (array $d): Delegacion {
            $o = new Delegacion();
            $o->setId_dl($d[0]);
            $o->setDl($d[1]);
            return $o;
        }, [[1, 'dlb'], [2, 'dlp'], [3, 'dlz']]));
        $s = new ResumenPlazasService($this->actividades, $this->plazas, $delegaciones, $this->ocupadas);
        $s->setId_activ(10);
        return $s;
    }

    public function test_plazas_de_calendario_cedidas_conseguidas_y_disponibles_por_dl(): void
    {
        $s = $this->svc();
        $this->assertSame([20, 15, 5, 0], [$s->getPlazasCalendario('dlb'), $s->getPlazasCalendario('dlp'), $s->getPlazasCalendario('dlz'), $s->getPlazasCalendario('dlq')]);
        $this->assertSame([3, 2, 0], [$s->getPlazasCedidas('dlb'), $s->getPlazasCedidas('dlp'), $s->getPlazasCedidas('dlz')]);
        $this->assertSame([0, 3, 2], [$s->getPlazasConseguidas('dlb'), $s->getPlazasConseguidas('dlp'), $s->getPlazasConseguidas('dlz')]);
        $this->assertSame([17, 16, 7], [$s->getPlazasDisponibles('dlb'), $s->getPlazasDisponibles('dlp'), $s->getPlazasDisponibles('dlz')]);
    }

    public function test_calendario_sin_fila_de_la_dl_organizadora_usa_la_de_la_propia_tabla(): void
    {
        $this->filas = [$this->fila(2, 'dlp', 9), $this->fila(2, 'dlx', 4)];
        $this->assertSame(9, $this->svc()->getPlazasCalendario('dlp'));
        $this->filas = [$this->fila(2, 'dlx', 4)];
        $this->assertSame(4, $this->svc()->getPlazasCalendario('dlp'), 'sin propia ni de la organizadora: cualquiera');
    }

    public function test_resumen_completo(): void
    {
        $r = $this->svc()->getResumen();

        $this->assertSame(['dlb', 'dlp', 'dlz', 'total'], array_keys($r));
        $this->assertSame(['actividad' => 40, 'calendario' => 40, 'cedidas' => 5, 'conseguidas' => 5, 'disponibles' => 40, 'ocupadas' => 26], $r['total']);

        $this->assertSame(20, $r['dlb']['calendario']);
        $this->assertSame(['dlp' => 3], $r['dlb']['cedidas']);
        $this->assertSame([3, 0], [$r['dlb']['total_cedidas'], $r['dlb']['total_conseguidas']]);
        $this->assertSame(['dlb' => 17], $r['dlb']['disponibles']);
        $this->assertSame(['dlb' => 10], $r['dlb']['ocupadas']);
        $this->assertSame([17, 10], [$r['dlb']['total_disponibles'], $r['dlb']['total_ocupadas']]);

        $this->assertSame(15, $r['dlp']['calendario']);
        $this->assertSame(['dlz' => 2], $r['dlp']['cedidas']);
        $this->assertSame(['dlb' => 3], $r['dlp']['conseguidas']);
        $this->assertSame(['dlp' => 13, 'dlb' => 3], $r['dlp']['disponibles']);
        $this->assertSame(['dlp' => 13, 'dlb' => 1], $r['dlp']['ocupadas']);
        $this->assertSame([16, 14], [$r['dlp']['total_disponibles'], $r['dlp']['total_ocupadas']]);

        $this->assertSame(['dlz' => 5, 'dlp' => 2], $r['dlz']['disponibles']);
        $this->assertSame([7, 2], [$r['dlz']['total_disponibles'], $r['dlz']['total_ocupadas']]);
    }

    public function test_libres_por_dl(): void
    {
        $s = $this->svc();
        $this->assertSame(7, $s->getLibres());
        $this->assertSame(7, $s->getLibres('dlb'));
        $this->assertSame(2, $s->getLibres('dlp'));
        $this->assertSame(5, $s->getLibres('dlz'));
        $this->assertSame(0, $s->getLibres('dlq'));
    }

    public function test_posibles_propietarios_desde_la_dl_organizadora(): void
    {
        $s = $this->svc();
        $this->assertSame(['dlb>dlb' => 'dlb (10 de 17)'], $s->getPosiblesPropietariosOpciones());
        // Persona de paso de dlp: puede ir con las plazas que dlb cede a dlp.
        $this->assertSame(['dlb>dlp' => 'dlp (1 de 3)', 'dlb>dlb' => 'dlb (10 de 17)'], $s->getPosiblesPropietariosOpciones('dlp'));
    }

    public function test_posibles_propietarios_desde_otra_dl(): void
    {
        $this->miDl('dlp');
        $s = $this->svc();
        $this->assertSame(['dlb>dlp' => 'dlb (1 de 3)', 'dlp>dlp' => 'dlp (13 de 13)'], $s->getPosiblesPropietariosOpciones());
        $this->assertSame('dlb>dlp', $s->getPrimeraPropiedadLibre());

        $this->miDl('dlz');
        $this->assertSame(['dlp>dlz' => 'dlp (2 de 2)', 'dlz>dlz' => 'dlz (0 de 5)'], $this->svc()->getPosiblesPropietariosOpciones());
    }

    public function test_sin_plazas_no_hay_propietarios(): void
    {
        $this->filas = [];
        $this->assertSame(['xxx' => 'no disponibles'], $this->svc()->getPosiblesPropietariosOpciones());
        $this->assertNull($this->svc()->getPrimeraPropiedadLibre());
    }

    public function test_propiedad_de_plaza_libre(): void
    {
        $this->assertSame(['success' => true, 'mensaje' => '', 'propiedad' => ['dlb>dlb' => 'dlb (10 de 17)']], $this->svc()->getPropiedadPlazaLibre());

        // dlp tiene ocupadas sus 13 propias: pasa a las cedidas por dlb.
        $this->miDl('dlp');
        $this->assertSame(
            ['success' => true, 'mensaje' => 'Ya están todas las plazas ocupadas', 'propiedad' => ['dlb>dlp' => 'dlb (1 de 3)']],
            $this->svc()->getPropiedadPlazaLibre()
        );

        // dlz: las 2 que le cede dlp están ocupadas, pero tiene 5 propias libres.
        $this->miDl('dlz');
        $this->assertSame(['dlz>dlz' => 'dlz (0 de 5)'], $this->svc()->getPropiedadPlazaLibre()['propiedad']);
    }

    public function test_sin_ninguna_plaza_libre(): void
    {
        $this->filas = [$this->fila(2, 'dlb', 13)];
        $this->miDl('dlp');
        $this->assertSame(['success' => false, 'mensaje' => 'Ya están todas las plazas ocupadas'], $this->svc()->getPropiedadPlazaLibre());
    }

    public function test_actividad_no_publicada_todas_las_plazas_son_de_la_organizadora(): void
    {
        $this->actividad->setPublicado(false);
        $this->filas = [];
        $s = $this->svc();
        $this->assertSame(['dlb>dlb' => 'dlb (10 de 40)'], $s->getPosiblesPropietariosOpciones());
        $this->assertSame(['concedidas' => 40, 'libres' => 30], $s->getPlazasConcedidasYLibres('dlb'));
    }

    /**
     * Hallazgo: si no se conocen las plazas (actividad sin plazas y sin casa), las
     * propias valen '?' y la comparación de texto '?' > 0 es cierta: se ofrece la
     * plaza «dlb (10 de ?)» y getPropiedadPlazaLibre la da por libre.
     */
    public function test_plazas_desconocidas_se_dan_por_libres(): void
    {
        $this->actividad->setPublicado(false);
        $this->actividad->setPlazas(null);
        $this->filas = [];
        $s = $this->svc();
        $this->assertSame('?', $s->getPlazasTotales());
        $this->assertSame(['dlb>dlb' => 'dlb (10 de ?)'], $s->getPosiblesPropietariosOpciones());
        $this->assertTrue($s->getPropiedadPlazaLibre()['success']);
    }

    public function test_actividad_inexistente(): void
    {
        $s = $this->svc();
        $s->setId_activ(99);
        $this->assertSame('?', $s->getPlazasTotales());
    }

    public function test_concedidas_y_libres_de_la_organizadora_y_con_ocupadas_desconocidas(): void
    {
        $this->assertSame(['concedidas' => 17, 'libres' => 7], $this->svc()->getPlazasConcedidasYLibres('dlb'));
        $this->ocupadas = $this->createMock(AsistenteActividadService::class);
        $this->ocupadas->method('getPlazasOcupadasPorDl')->willReturn(-1);
        $this->assertSame(['concedidas' => 17, 'libres' => '-'], $this->svc()->getPlazasConcedidasYLibres('dlb'));
    }
}
