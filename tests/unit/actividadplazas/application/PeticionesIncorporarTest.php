<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\PeticionesIncorporar;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\entity\PlazaPeticion;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/** Incorporar la primera petición de plaza de cada persona como asistencia con plaza asignada. */
final class PeticionesIncorporarTest extends myTest
{
    private ActividadDlRepositoryInterface&MockObject $dl;
    private ActividadPubRepositoryInterface&MockObject $pub;
    private ActividadRepositoryInterface&MockObject $todas;
    private PlazaPeticionRepositoryInterface&MockObject $peticiones;
    private AsistenteDlRepositoryInterface&MockObject $asistentesDl;
    private AsistenteOutRepositoryInterface&MockObject $asistentesOut;
    private PlazaPropietarioAsignacionInterface&MockObject $propietario;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<string> */
    private array $filtrosPeticion = [];
    /** @var list<PlazaPeticion> */
    private array $peticionesDevueltas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oConfig', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $curso = ['ini_dia' => 1, 'ini_mes' => 9, 'fin_dia' => 31, 'fin_mes' => 8];
        $_SESSION['oConfig'] = new ConfigSnapshot(null, null, null, null, null, null, 'dl', null, null, null, 'es_ES.UTF-8', null, null, $curso, $curso);
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->dl = $this->createMock(ActividadDlRepositoryInterface::class);
        $this->pub = $this->createMock(ActividadPubRepositoryInterface::class);
        $this->todas = $this->createMock(ActividadRepositoryInterface::class);
        $this->peticiones = $this->createMock(PlazaPeticionRepositoryInterface::class);
        $this->peticiones->method('matching')->willReturnCallback(function (Criteria $c): array {
            $this->filtrosPeticion = DescribeCriteria::filtros($c);

            return $this->peticionesDevueltas;
        });
        $this->asistentesDl = $this->createMock(AsistenteDlRepositoryInterface::class);
        $this->asistentesOut = $this->createMock(AsistenteOutRepositoryInterface::class);
        $this->propietario = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $this->propietario->method('asegurar')->willReturn('');
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function uc(): PeticionesIncorporar
    {
        return new PeticionesIncorporar($this->dl, $this->pub, $this->todas, $this->peticiones, $this->asistentesDl, $this->asistentesOut, $this->propietario);
    }

    private function actividad(int $id, string $dlOrg): ActividadAll
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getId_activ')->willReturn($id);
        $a->method('getDl_org')->willReturn($dlOrg);

        return $a;
    }

    private function peticion(int $idNom, int $idActiv): PlazaPeticion
    {
        $p = $this->createMock(PlazaPeticion::class);
        $p->method('getId_nom')->willReturn($idNom);
        $p->method('getId_activ')->willReturn($idActiv);

        return $p;
    }

    private function asistenteExistente(int $idActiv): Asistente
    {
        $a = $this->createMock(Asistente::class);
        $a->method('getId_activ')->willReturn($idActiv);

        return $a;
    }

    private const NUMERARIOS = ['sasistentes' => 'n', 'sactividad' => 'ca'];

    public function test_las_peticiones_buscadas_son_las_de_primer_orden_del_tipo_y_del_grupo_de_personas(): void
    {
        $this->todas->method('matching')->willReturn([]);

        $this->uc()->execute(self::NUMERARIOS);
        $n = $this->filtrosPeticion;
        $this->uc()->execute(['sasistentes' => 'agd', 'sactividad' => 'crt']);
        $a = $this->filtrosPeticion;

        $this->assertSame(['orden eq 1', 'tipo eq ca', 'id_nom matches ^\d{4}1'], $n);
        $this->assertSame(['orden eq 1', 'tipo eq crt', 'id_nom matches ^\d{4}2'], $a);
    }

    public function test_una_peticion_de_una_actividad_de_mi_dl_se_incorpora_como_asignada_con_propietario_dl_origen_destino(): void
    {
        $this->todas->method('matching')->willReturn([$this->actividad(3001, 'dlb')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 3001)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([]);
        $guardado = null;
        $this->asistentesDl->expects($this->once())->method('Guardar')->willReturnCallback(function (Asistente $a) use (&$guardado): bool {
            $guardado = $a;

            return true;
        });
        $this->asistentesOut->expects($this->never())->method('Guardar');

        $r = $this->uc()->execute(self::NUMERARIOS);

        $this->assertSame(1, $r['incorporadas']);
        $this->assertSame('', $r['error']);
        $this->assertSame([3001, 10011, 'dlb>dlb', 'dlb'], [$guardado->getId_activ(), $guardado->getId_nom(), $guardado->getPropietario(), $guardado->getDl_responsable()]);
        $this->assertTrue($guardado->isPropio());
    }

    public function test_una_actividad_de_otra_dl_se_guarda_en_el_repositorio_de_fuera_y_se_quita_la_f_final(): void
    {
        $this->todas->method('matching')->willReturn([$this->actividad(3002, 'dlpf')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 3002)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([]);
        $guardado = null;
        $this->asistentesOut->expects($this->once())->method('Guardar')->willReturnCallback(function (Asistente $a) use (&$guardado): bool {
            $guardado = $a;

            return true;
        });
        $this->asistentesDl->expects($this->never())->method('Guardar');

        $this->uc()->execute(self::NUMERARIOS);

        $this->assertSame('dlp>dlb', $guardado->getPropietario());
    }

    public function test_se_ignoran_las_peticiones_de_actividades_fuera_del_listado_y_de_quien_ya_va_a_otra(): void
    {
        $this->todas->method('matching')->willReturn([$this->actividad(3001, 'dlb'), $this->actividad(3003, 'dlb')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 9999), $this->peticion(10012, 3001), $this->peticion(10013, 3001)];
        $this->asistentesDl->method('matching')->willReturnCallback(function (Criteria $c): array {
            return in_array('id_nom eq 10012', DescribeCriteria::filtros($c), true) ? [$this->asistenteExistente(3003)] : [];
        });
        $this->asistentesOut->method('matching')->willReturn([]);
        $this->asistentesDl->expects($this->once())->method('Guardar')->willReturn(true);

        $this->assertSame(1, $this->uc()->execute(self::NUMERARIOS)['incorporadas']);
    }

    public function test_tambien_cuenta_como_ya_asistente_quien_va_a_una_actividad_de_fuera_del_listado_de_mi_dl(): void
    {
        $this->todas->method('matching')->willReturn([$this->actividad(3001, 'dlb')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 3001)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([$this->asistenteExistente(3001)]);
        $this->asistentesDl->expects($this->never())->method('Guardar');

        $this->assertSame(0, $this->uc()->execute(self::NUMERARIOS)['incorporadas']);
    }

    public function test_un_error_de_plaza_se_devuelve_y_no_se_guarda_esa_asistencia(): void
    {
        $this->propietario = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $this->propietario->method('asegurar')->willReturn('sin plaza libre');
        $this->todas->method('matching')->willReturn([$this->actividad(3001, 'dlb')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 3001)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([]);
        $this->asistentesDl->expects($this->never())->method('Guardar');

        $r = $this->uc()->execute(self::NUMERARIOS);

        $this->assertSame([0, 'sin plaza libre'], [$r['incorporadas'], $r['error']]);
    }

    public function test_un_fallo_de_guardado_se_informa(): void
    {
        $this->todas->method('matching')->willReturn([$this->actividad(3001, 'dlb')]);
        $this->peticionesDevueltas = [$this->peticion(10011, 3001)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([]);
        $this->asistentesDl->method('Guardar')->willReturn(false);

        $r = $this->uc()->execute(self::NUMERARIOS);

        $this->assertSame(0, $r['incorporadas']);
        $this->assertNotSame('', $r['error']);
    }

    public function test_para_agregados_se_buscan_en_dl_y_publicas_de_otras_dl_y_el_mensaje_nombra_el_curso(): void
    {
        $this->dl->method('matching')->willReturn([$this->actividad(3001, 'dlb')]);
        $filtrosPub = [];
        $this->pub->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtrosPub): array {
            $filtrosPub = DescribeCriteria::filtros($c);

            return [$this->actividad(3005, 'dlp')];
        });
        $this->todas->expects($this->never())->method('matching');
        $this->peticionesDevueltas = [$this->peticion(20011, 3005)];
        $this->asistentesDl->method('matching')->willReturn([]);
        $this->asistentesOut->method('matching')->willReturn([]);
        $this->asistentesOut->expects($this->once())->method('Guardar')->willReturn(true);

        $r = $this->uc()->execute(['sasistentes' => 'agd', 'sactividad' => 'ca']);

        $this->assertContains('dl_org neq dlb', $filtrosPub);
        $this->assertSame(1, $r['incorporadas']);
        $this->assertMatchesRegularExpression('/\d{4}-\d\d-\d\d - \d{4}-\d\d-\d\d/', $r['mensaje_final']);
    }
}
