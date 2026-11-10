<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividadplazas\application\PeticionesActivData;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\shared\domain\criteria\Criteria;
use src\shared\security\HashB;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;
use Tests\unit\planning\support\DescribeCriteria;

/** Pantalla de peticiones de actividades: candidatas por tipo/curso y depuración de peticiones obsoletas. */
final class PeticionesActivDataTest extends myTest
{
    private PersonaDlRepositoryInterface&MockObject $personas;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private ActividadDlRepositoryInterface&MockObject $actDl;
    private ActividadPubRepositoryInterface&MockObject $actPub;
    private PlazaPeticionRepositoryInterface&MockObject $peticiones;
    private PeticionesActivData $uc;
    private array $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION;
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv', 'sfsv' => 1];
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, null, null, null, null, null, null, null, $curso, $curso);

        $this->personas = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->actDl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->actPub = $this->createMock(ActividadPubRepositoryInterface::class);
        $this->peticiones = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $this->uc = new PeticionesActivData($this->personas, $this->delegaciones, $this->actDl, $this->actPub, $this->peticiones);
    }

    public function tearDown(): void
    {
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    private function actividad(int $id, string $nom): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getNom_activ')->willReturn($nom);

        return $a;
    }

    private function peticion(int $idActiv): PlazaPeticion
    {
        $p = $this->createMock(PlazaPeticion::class);
        $p->method('getId_activ')->willReturn($idActiv);

        return $p;
    }

    /** @var array{0: ?Criteria, 1: ?Criteria} criterios usados en dl y pub */
    private array $c = [null, null];

    private function capturar(array $dl = [], array $pub = []): void
    {
        $this->actDl->method('matching')->willReturnCallback(function (Criteria $x) use ($dl): array {
            $this->c[0] = $x;

            return $dl;
        });
        $this->actPub->method('matching')->willReturnCallback(function (Criteria $x) use ($pub): array {
            $this->c[1] = $x;

            return $pub;
        });
    }

    public function test_numerarios_en_curso_academico_buscan_tipo_112_del_curso_y_separan_publicadas_con_guion(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->peticiones->method('matching')->willReturn([]);
        $this->capturar([$this->actividad(100, 'Ca propia')], [$this->actividad(200, 'Ca otra')]);

        $r = $this->uc->execute(['id_nom' => '10011', 'na' => 'n', 'sactividad' => 'ca', 'todos' => 1]);

        $anyFin = (int) date('m') > 8 ? (int) date('Y') + 1 : (int) date('Y');
        $filtros = DescribeCriteria::filtros($this->c[0]);
        $this->assertSame('f_ini between [' . ($anyFin - 1) . '-09-01,' . $anyFin . '-08-31]', $filtros[0]);
        $this->assertSame('id_tipo_activ matches ^112', $filtros[2]);
        $this->assertContains('dl_org neq dlb', DescribeCriteria::filtros($this->c[1]));
        $this->assertSame([100 => 'Ca propia', 1 => '--------', 200 => 'Ca otra'], $r['opciones'], 'el separador queda entre las propias y las publicadas');
        $this->assertSame('', $r['ap_nom']);
        $this->assertSame(['ca', 'ca'], [$r['sactividad'], $r['tipo']]);
    }

    public function test_agregados_en_ca_se_tratan_como_cv_y_usan_tipo_133_o_131_en_cr(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->peticiones->method('matching')->willReturn([]);
        $this->capturar();

        $r = $this->uc->execute(['id_nom' => '1', 'na' => 'agd', 'sactividad' => 'ca', 'todos' => 0]);

        $this->assertSame('cv', $r['sactividad']);
        $this->assertContains('id_tipo_activ matches ^133', DescribeCriteria::filtros($this->c[0]));
    }

    public function test_que_hace_de_sactividad_cuando_esta_vacia_y_en_cr_busca_tipo_x1(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->peticiones->method('matching')->willReturn([]);
        $this->capturar();

        $r = $this->uc->execute(['id_nom' => '1', 'na' => 'n', 'que' => 'crt', 'todos' => 0]);

        $this->assertSame('crt', $r['sactividad']);
        $this->assertContains('id_tipo_activ matches ^111', DescribeCriteria::filtros($this->c[0]));
    }

    public function test_el_grupo_de_estudios_filtra_por_dl_origen_con_varias_delegaciones(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->peticiones->method('matching')->willReturn([]);
        $d1 = new Delegacion();
        $d1->setDl('dlx');
        $d2 = new Delegacion();
        $d2->setDl('dly');
        $this->delegaciones->method('matching')->willReturn([$d1, $d2]);
        $this->capturar();

        $this->uc->execute(['id_nom' => '1', 'na' => 'n', 'sactividad' => 'ca', 'todos' => 3]);

        $this->assertContains('dl_org in [dlx,dly]', DescribeCriteria::filtros($this->c[0]));
        $this->assertNotContains('dl_org in [dlx,dly]', DescribeCriteria::filtros($this->c[1]), 'las publicadas no se filtran por grupo');
    }

    /**
     * Hallazgo (ya comentado en el código): con una sola delegación en el grupo el valor se compara por
     * igualdad con el texto entrecomillado `'dlx'`, que no coincide con ningún dl_org real.
     */
    public function test_un_grupo_de_una_sola_delegacion_compara_con_el_texto_entrecomillado(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->peticiones->method('matching')->willReturn([]);
        $d1 = new Delegacion();
        $d1->setDl('dlx');
        $this->delegaciones->method('matching')->willReturn([$d1]);
        $this->capturar();

        $this->uc->execute(['id_nom' => '1', 'na' => 'n', 'sactividad' => 'ca', 'todos' => 3]);

        $this->assertContains("dl_org eq 'dlx'", DescribeCriteria::filtros($this->c[0]));
    }

    public function test_se_borran_las_peticiones_que_ya_no_estan_en_la_lista_y_se_devuelven_las_vigentes_en_orden(): void
    {
        $persona = $this->createMock(PersonaDl::class);
        $persona->method('getPrefApellidosNombre')->willReturn('Gil, Ana');
        $this->personas->method('findById')->willReturn($persona);
        $this->capturar([$this->actividad(1, 'A'), $this->actividad(2, 'B')]);
        $vigente1 = $this->peticion(2);
        $vigente2 = $this->peticion(1);
        $obsoleta = $this->peticion(99);
        $this->peticiones->method('matching')->willReturn([$vigente1, $obsoleta, $vigente2]);
        $this->peticiones->expects($this->once())->method('Eliminar')->with($obsoleta);

        $r = $this->uc->execute(['id_nom' => '10011', 'na' => 'n', 'sactividad' => 'ca', 'todos' => 1]);

        $this->assertSame(['Gil, Ana', '2,1'], [$r['ap_nom'], $r['sid_activ']]);
        $this->assertSame(['id_nom' => 10011, 'sactividad' => 'ca'], HashB::open($r['ctx_guardar'], 'peticiones_guardar'));
        $this->assertSame(['id_nom' => 10011, 'sactividad' => 'ca'], HashB::open($r['ctx_eliminar'], 'peticiones_eliminar'));
    }

    public function test_un_tipo_de_persona_desconocido_no_ofrece_actividades_y_borra_todas_las_peticiones(): void
    {
        $this->personas->method('findById')->willReturn(null);
        $this->actDl->expects($this->never())->method('matching');
        $this->peticiones->method('matching')->willReturn([$this->peticion(5)]);
        $this->peticiones->expects($this->once())->method('Eliminar');

        $r = $this->uc->execute(['id_nom' => '1', 'na' => 'x', 'sactividad' => 'ca']);

        $this->assertSame([[], ''], [$r['opciones'], $r['sid_activ']]);
    }
}
