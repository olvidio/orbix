<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\services\PropuestasAprobarService;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacd;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\support\DescribeCriteria;

final class PropuestasAprobarServiceTest extends TestCase
{
    private PropuestaEncargoSacdRepositoryInterface&MockObject $propuestas;
    private PropuestaEncargoSacdHorarioRepositoryInterface&MockObject $propuestaHorarios;
    private EncargoSacdRepositoryInterface&MockObject $encargosSacd;
    private EncargoSacdHorarioRepositoryInterface&MockObject $horarios;
    private int $limpiezas = 0;
    /** @var list<string> */
    private array $orden = [];
    private PropuestasAprobarService $servicio;

    protected function setUp(): void
    {
        $this->propuestas = $this->createMock(PropuestaEncargoSacdRepositoryInterface::class);
        $this->propuestaHorarios = $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class);
        $this->encargosSacd = $this->createMock(EncargoSacdRepositoryInterface::class);
        $this->horarios = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $this->limpiezas = 0;
        $this->orden = [];
        $this->servicio = new PropuestasAprobarService(
            $this->propuestas, $this->propuestaHorarios, $this->encargosSacd, $this->horarios,
            function (): void {
                $this->limpiezas++;
                $this->orden[] = 'limpieza';
            }
        );
    }

    private function propuesta(int $idItem, int $idEnc, int $idNom, ?int $idNomNew, int $modo = 3): PropuestaEncargoSacd
    {
        $p = new PropuestaEncargoSacd();
        $p->setId_item($idItem);
        $p->setId_enc($idEnc);
        $p->setId_nom($idNom);
        $p->setModoVo($modo);
        $p->setF_ini(new DateTimeLocal('2024-01-01'));
        $p->setId_nom_new($idNomNew);

        return $p;
    }

    private function horarioPropuesta(?string $diaRef, ?int $diaInc, int $idEnc = 5, int $idNom = 10011): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_item(1);
        $h->setId_enc($idEnc);
        $h->setId_nom($idNom);
        $h->setDia_ref($diaRef);
        $h->setDia_inc($diaInc);

        return $h;
    }

    private function conPropuestas(PropuestaEncargoSacd ...$p): void
    {
        $this->propuestas->method('matching')->willReturn(array_values($p));
    }

    /** @param list<EncargoSacdHorario> $lista */
    private function horariosPropuesta(array $lista): void
    {
        $this->propuestaHorarios->method('matching')->willReturn($lista);
    }

    private function hoy(): string
    {
        return date('Y-m-d');
    }

    // ---- general --------------------------------------------------------------------------------------

    public function test_sin_propuestas_solo_limpia_las_tablas_y_devuelve_el_aviso(): void
    {
        $this->propuestas->method('matching')->willReturn([]);
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $texto = $this->servicio->execute();

        $this->assertNotSame('', $texto);
        $this->assertSame(1, $this->limpiezas);
    }

    public function test_la_limpieza_se_hace_una_sola_vez_y_al_final(): void
    {
        $this->conPropuestas($this->propuesta(1, 5, 0, 10011), $this->propuesta(2, 6, 0, 10012));
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->encargosSacd->method('Guardar')->willReturnCallback(function () {
            $this->orden[] = 'guardar';

            return true;
        });
        $this->horariosPropuesta([]);

        $this->servicio->execute();

        $this->assertSame(['guardar', 'guardar', 'limpieza'], $this->orden);
    }

    public function test_sin_titular_actual_ni_nuevo_no_se_hace_nada(): void
    {
        $this->conPropuestas($this->propuesta(1, 5, 0, null), $this->propuesta(2, 5, 0, 0));
        $this->encargosSacd->expects($this->never())->method('Guardar');
        $this->encargosSacd->expects($this->never())->method('findById');

        $this->servicio->execute();

        $this->assertSame(1, $this->limpiezas);
    }

    // ---- nuevo titular (no había) ----------------------------------------------------------------------

    public function test_un_titular_nuevo_crea_el_encargo_del_sacd_desde_hoy_y_sin_fin(): void
    {
        $this->conPropuestas($this->propuesta(1, 5, 0, 10099, 2));
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $guardado = null;
        $this->encargosSacd->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoSacd $o) use (&$guardado): bool {
            $guardado = $o;

            return true;
        });
        $this->horariosPropuesta([]);

        $this->servicio->execute();

        $this->assertSame(900, $guardado->getId_item());
        $this->assertSame(5, $guardado->getId_enc());
        $this->assertSame(10099, $guardado->getId_nom());
        $this->assertSame(2, $guardado->getModo(), 'conserva el modo de la propuesta');
        $this->assertSame($this->hoy(), $guardado->getF_ini()->format('Y-m-d'));
        $this->assertNull($guardado->getF_fin());
    }

    public function test_el_titular_nuevo_hereda_los_horarios_de_la_propuesta_con_los_ids_nuevos(): void
    {
        $this->conPropuestas($this->propuesta(1, 5, 0, 10099));
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $this->horariosPropuesta([$this->horarioPropuesta('m', 3), $this->horarioPropuesta('v', 4)]);
        $this->horarios->method('getNewId')->willReturnOnConsecutiveCalls(800, 801);
        $guardados = [];
        $this->horarios->expects($this->exactly(2))->method('Guardar')->willReturnCallback(function (EncargoSacdHorario $h) use (&$guardados): bool {
            $guardados[] = [$h->getId_item(), $h->getId_enc(), $h->getId_nom(), $h->getDia_ref(), $h->getDia_inc(), $h->getId_item_tarea_sacd(), $h->getF_ini()->format('Y-m-d')];

            return true;
        });

        $this->servicio->execute();

        $this->assertSame([
            [800, 5, 10099, 'm', 3, 900, $this->hoy()],
            [801, 5, 10099, 'v', 4, 900, $this->hoy()],
        ], $guardados);
    }

    public function test_los_horarios_de_la_propuesta_se_buscan_por_la_tarea_y_con_sacd(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 0, 10099));
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $this->propuestaHorarios->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_item_tarea_sacd eq 41', 'id_nom is_not_null'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);

        $this->servicio->execute();
    }

    // ---- se queda sin titular -------------------------------------------------------------------------------

    public function test_sin_titular_nuevo_se_cierra_el_encargo_actual_hoy(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, null));
        $actual = new EncargoSacd();
        $actual->setId_item(41);
        $actual->setId_enc(5);
        $actual->setId_nom(10011);
        $actual->setModoVo(3);
        $actual->setF_ini(new DateTimeLocal('2020-01-01'));
        $this->encargosSacd->method('findById')->with(41)->willReturn($actual);
        $this->encargosSacd->expects($this->once())->method('Guardar')->with($actual)->willReturn(true);
        $this->horariosPropuesta([]);

        $this->servicio->execute();

        $this->assertSame($this->hoy(), $actual->getF_fin()->format('Y-m-d'));
    }

    public function test_si_el_encargo_actual_ya_no_existe_no_se_hace_nada(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, null));
        $this->encargosSacd->method('findById')->willReturn(null);
        $this->encargosSacd->expects($this->never())->method('Guardar');
        $this->propuestaHorarios->expects($this->never())->method('matching');

        $this->servicio->execute();
    }

    /**
     * Hallazgo: al cerrar el encargo se pone fecha de fin a los horarios de la PROPUESTA, pero los
     * horarios REALES del sacd saliente (EncargoSacdHorario) no se tocan: siguen abiertos.
     */
    public function test_al_cerrar_el_encargo_se_cierran_los_horarios_de_la_propuesta_pero_no_los_reales(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, null));
        $actual = new EncargoSacd();
        $actual->setId_item(41);
        $actual->setId_enc(5);
        $actual->setId_nom(10011);
        $actual->setModoVo(3);
        $actual->setF_ini(new DateTimeLocal('2020-01-01'));
        $this->encargosSacd->method('findById')->willReturn($actual);
        $this->encargosSacd->method('Guardar')->willReturn(true);
        $hp = $this->horarioPropuesta('m', 3);
        $this->horariosPropuesta([$hp]);
        $this->propuestaHorarios->expects($this->once())->method('Guardar')->with($hp)->willReturn(true);
        $this->horarios->expects($this->never())->method('Guardar');
        $this->horarios->expects($this->never())->method('matching');

        $this->servicio->execute();

        $this->assertSame($this->hoy(), $hp->getF_fin()->format('Y-m-d'));
    }

    // ---- mismo titular: se concilian los horarios ----------------------------------------------------------------------

    public function test_mismo_titular_con_horario_distinto_actualiza_la_dedicacion(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, 10011));
        $this->horariosPropuesta([$this->horarioPropuesta('m', 5)]);
        $actual = $this->horarioPropuesta('m', 2);
        $this->horarios->method('matching')->willReturn([$actual]);
        $this->horarios->expects($this->once())->method('Guardar')->with($actual)->willReturn(true);
        $this->encargosSacd->expects($this->never())->method('Guardar');

        $this->servicio->execute();

        $this->assertSame(5, $actual->getDia_inc());
    }

    public function test_mismo_titular_con_el_mismo_horario_no_guarda_nada(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, 10011));
        $this->horariosPropuesta([$this->horarioPropuesta('m', 5)]);
        $this->horarios->method('matching')->willReturn([$this->horarioPropuesta('m', 5)]);
        $this->horarios->expects($this->never())->method('Guardar');

        $this->servicio->execute();
    }

    public function test_mismo_titular_con_un_modulo_sin_horario_actual_lo_crea_desde_hoy(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, 10011));
        $this->horariosPropuesta([$this->horarioPropuesta('t', 6, 5, 10011)]);
        $this->horarios->method('matching')->willReturn([]);
        $this->horarios->method('getNewId')->willReturn(800);
        $creado = null;
        $this->horarios->expects($this->once())->method('Guardar')->willReturnCallback(function (EncargoSacdHorario $h) use (&$creado): bool {
            $creado = $h;

            return true;
        });

        $this->servicio->execute();

        $this->assertSame([800, 5, 10011, 't', 6, 41], [$creado->getId_item(), $creado->getId_enc(), $creado->getId_nom(), $creado->getDia_ref(), $creado->getDia_inc(), $creado->getId_item_tarea_sacd()]);
        $this->assertSame($this->hoy(), $creado->getF_ini()->format('Y-m-d'));
    }

    public function test_el_horario_actual_se_busca_por_tarea_modulo_con_sacd_y_vigente(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, 10011));
        $this->horariosPropuesta([$this->horarioPropuesta('v', 6)]);
        $this->horarios->expects($this->once())->method('matching')->with($this->callback(function (Criteria $c): bool {
            $this->assertSame(['id_item_tarea_sacd eq 41', 'dia_ref eq v', 'id_nom is_not_null', 'f_fin is_null'], DescribeCriteria::filtros($c));

            return true;
        }))->willReturn([]);
        $this->horarios->method('getNewId')->willReturn(800);
        $this->horarios->method('Guardar')->willReturn(true);

        $this->servicio->execute();
    }

    // ---- cambio de titular ---------------------------------------------------------------------------------------------

    public function test_cambio_de_titular_cierra_el_actual_y_crea_el_nuevo(): void
    {
        $this->conPropuestas($this->propuesta(41, 5, 10011, 10099));
        $actual = new EncargoSacd();
        $actual->setId_item(41);
        $actual->setId_enc(5);
        $actual->setId_nom(10011);
        $actual->setModoVo(3);
        $actual->setF_ini(new DateTimeLocal('2020-01-01'));
        $this->encargosSacd->method('findById')->willReturn($actual);
        $this->encargosSacd->method('getNewId')->willReturn(900);
        $guardados = [];
        $this->encargosSacd->expects($this->exactly(2))->method('Guardar')->willReturnCallback(function (EncargoSacd $o) use (&$guardados): bool {
            $guardados[] = [$o->getId_nom(), $o->getF_fin()?->format('Y-m-d')];

            return true;
        });
        $this->horariosPropuesta([]);

        $this->servicio->execute();

        $this->assertSame([[10011, $this->hoy()], [10099, null]], $guardados, 'primero se cierra el actual y luego se crea el nuevo');
    }
}
