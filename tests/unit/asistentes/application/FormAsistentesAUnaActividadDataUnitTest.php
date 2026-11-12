<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Formulario de asistencia desde la ficha de una actividad (dossier 3101): alta (desplegable de personas según el
 * tipo) y edición (datos de la asistencia, tipo de persona, cupo de plazas). Los avisos de propietario y el
 * asistente inexistente están en el test de integración.
 */
final class FormAsistentesAUnaActividadDataUnitTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const ID_ACTIV = 3001;

    private AsistenteRepositoryInterface&MockObject $asistentes;
    private PersonaNRepositoryInterface&MockObject $n;
    private PersonaNaxRepositoryInterface&MockObject $nax;
    private PersonaAgdRepositoryInterface&MockObject $agd;
    private PersonaSRepositoryInterface&MockObject $s;
    private PersonaExRepositoryInterface&MockObject $ex;
    private ResumenPlazasService&MockObject $plazas;
    /** @var array<int, object> */
    private array $personas = [];
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->anterior['config'] = $_SESSION['config'] ?? null;
        $this->plazasInstaladas(false);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?object => $this->personas[$id] ?? null);
        $this->sustituirServicio(PersonaFinderService::class, $finder);

        $this->asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $this->n = $this->createMock(PersonaNRepositoryInterface::class);
        $this->n->method('getArrayPersonas')->willReturn([10011 => 'Pla, Joan']);
        $this->nax = $this->createMock(PersonaNaxRepositoryInterface::class);
        $this->nax->method('getArrayPersonas')->willReturn([10021 => 'Nax, Uno']);
        $this->agd = $this->createMock(PersonaAgdRepositoryInterface::class);
        $this->agd->method('getArrayPersonas')->willReturn([10031 => 'Agd, Uno']);
        $this->s = $this->createMock(PersonaSRepositoryInterface::class);
        $this->s->method('getArrayPersonas')->willReturn([10041 => 'S, Uno']);
        $this->ex = $this->createMock(PersonaExRepositoryInterface::class);
        $this->ex->method('getArrayPersonas')->willReturnCallback(static fn (string $tabla = ''): array => ['tabla' => $tabla]);
        $this->plazas = $this->createMock(ResumenPlazasService::class);
        $this->plazas->method('getPosiblesPropietariosOpciones')->willReturnCallback(static fn (string|false $paso = false): array => ['paso' => (string) $paso]);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        if ($this->anterior['config'] === null) {
            unset($_SESSION['config']);
        } else {
            $_SESSION['config'] = $this->anterior['config'];
        }
        parent::tearDown();
    }

    private function plazasInstaladas(bool $si): void
    {
        $config = is_array($_SESSION['config'] ?? null) ? $_SESSION['config'] : [];
        $config['a_apps'] = ['actividadplazas' => 971];
        $config['app_installed'] = $si ? [971] : [];
        $_SESSION['config'] = $config;
    }

    private function uc(): FormAsistentesAUnaActividadData
    {
        return new FormAsistentesAUnaActividadData(
            $this->asistentes,
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->n,
            $this->nax,
            $this->agd,
            $this->s,
            $this->ex,
            $this->plazas,
        );
    }

    /**
     * @return iterable<string, array{array<string, string>, mixed, string}>
     */
    public static function altaProvider(): iterable
    {
        yield 'n' => [['obj_pau' => 'PersonaN'], [10011 => 'Pla, Joan'], 'PersonaN'];
        yield 'nax' => [['obj_pau' => 'PersonaNax'], [10021 => 'Nax, Uno'], 'PersonaNax'];
        yield 'agd' => [['obj_pau' => 'PersonaAgd'], [10031 => 'Agd, Uno'], 'PersonaAgd'];
        yield 's' => [['obj_pau' => 'PersonaS'], [10041 => 'S, Uno'], 'PersonaS'];
        yield 'sss+: los de paso de sss+' => [['obj_pau' => 'PersonaSSSC', 'na' => 'sssc'], ['tabla' => 'psssc'], 'PersonaEx'];
        yield 'de paso con na aparte' => [['obj_pau' => 'PersonaEx', 'na' => 'n'], ['tabla' => 'pn'], 'PersonaEx'];
        yield 'de paso con na dentro de obj_pau (enlace antiguo)' => [['obj_pau' => 'PersonaEx&na=a'], ['tabla' => 'pa'], 'PersonaEx'];
        yield 'de paso codificado' => [['obj_pau' => 'PersonaEx%26na%3Dx'], ['tabla' => 'px'], 'PersonaEx'];
        yield 'desconocido' => [['obj_pau' => 'Otra'], [], 'Otra'];
    }

    #[DataProvider('altaProvider')]
    public function test_el_alta_ofrece_las_personas_del_tipo_pedido(array $entrada, mixed $opciones, string $objPau): void
    {
        $r = $this->uc()->build(['id_pau' => (string) self::ID_ACTIV] + $entrada);

        $this->assertSame($opciones, $r['personas_opciones']);
        $this->assertNull($r['personas_onchange']);
        $this->assertSame(['id_activ' => self::ID_ACTIV, 'obj_pau' => $objPau, 'mod' => 'nuevo', 'actualizar' => 0], $r['hash_main']['campos_hidden']);
        $this->assertSame('observ!observ_est!id_nom', $r['hash_main']['campos_form']);
        $this->assertSame(['checked', '', ''], [$r['propio_chk'], $r['falta_chk'], $r['est_chk']], 'por defecto, propio');
    }

    public function test_con_plazas_el_alta_cambia_el_propietario_al_elegir_persona_y_ofrece_plazas_y_cupos(): void
    {
        $this->plazasInstaladas(true);
        $this->plazas->expects($this->once())->method('setId_activ')->with(self::ID_ACTIV);

        $r = $this->uc()->build(['id_activ' => self::ID_ACTIV, 'obj_pau' => 'PersonaN']);

        $this->assertSame('fnjs_cmb_propietario()', $r['personas_onchange']);
        $this->assertSame(PlazaId::getArrayPosiblesPlazas(), $r['plaza_opciones']);
        $this->assertSame(['paso' => ''], $r['propietario_opciones']);
        $this->assertTrue($r['propietario_select_blanco']);
        $this->assertSame('observ!observ_est!plaza!propietario!id_nom', $r['hash_main']['campos_form']);
        $this->assertSame((string) PlazaId::PEDIDA, $r['plaza_selected']);
    }

    private function personaDl(int $id, string $idTabla): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getId_tabla')->willReturn($idTabla);
        $p->method('getPrefApellidosNombre')->willReturn('Pla, Joan');
        $this->personas[$id] = $p;
    }

    private function asistencia(int $idNom, ?string $propietario = null, ?int $plaza = PlazaId::ASIGNADA): void
    {
        $a = new Asistente();
        $a->setId_activ(self::ID_ACTIV);
        $a->setId_nom($idNom);
        $a->setPropio(false);
        $a->setFalta(true);
        $a->setEst_ok(true);
        $a->setObserv('llega tarde');
        $a->setObservEstVo('convalida');
        $a->setPlaza($plaza);
        $a->setPropietario($propietario);
        $this->asistentes->method('matching')->willReturn([$a]);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function tiposPersonaProvider(): iterable
    {
        yield 'n' => ['n', 'PersonaN'];
        yield 'agd' => ['a', 'PersonaAgd'];
        yield 's' => ['s', 'PersonaS'];
        yield 'nax (id_tabla x)' => ['x', 'PersonaNax'];
        yield 'sss+' => ['sssc', 'PersonaSSSC'];
        yield 'n de paso' => ['pn', 'PersonaEx'];
        yield 'nax de paso' => ['px', 'PersonaEx'];
    }

    /**
     * Regresión: el tipo se deducía con un `switch` que esperaba «nax» (el id_tabla es «x») y no conocía «px» ni
     * «psssc»; ahora usa el mapa de `PersonaRepositoryResolver`.
     */
    #[DataProvider('tiposPersonaProvider')]
    public function test_la_edicion_deduce_el_tipo_de_persona_de_su_id_tabla(string $idTabla, string $objPau): void
    {
        $this->personaDl(10011, $idTabla);
        $this->asistencia(10011);

        $r = $this->uc()->build(['sel' => ['10011#x'], 'id_pau' => self::ID_ACTIV, 'obj_pau' => 'Lo que venga']);

        $this->assertSame($objPau, $r['hash_main']['campos_hidden']['obj_pau']);
    }

    public function test_la_edicion_carga_los_datos_de_la_asistencia(): void
    {
        $this->personaDl(10011, 'n');
        $this->asistencia(10011, null, PlazaId::CONFIRMADA);

        $r = $this->uc()->build(['id_nom' => '10011', 'id_activ' => (string) self::ID_ACTIV]);

        $this->assertSame(['10011', 'Pla, Joan'], [$r['id_nom_real'], $r['ape_nom']]);
        $this->assertSame(['', 'checked', 'checked'], [$r['propio_chk'], $r['falta_chk'], $r['est_chk']]);
        $this->assertSame(['llega tarde', 'convalida'], [$r['observ'], $r['observ_est']]);
        $this->assertSame((string) PlazaId::CONFIRMADA, $r['plaza_selected']);
        $this->assertSame(['id_activ' => self::ID_ACTIV, 'obj_pau' => 'PersonaN', 'mod' => 'editar', 'actualizar' => 0, 'id_nom' => 10011], $r['hash_main']['campos_hidden']);
        $this->assertSame('observ!observ_est', $r['hash_main']['campos_form']);
        $this->assertArrayNotHasKey('personas_opciones', $r);
    }

    public function test_editar_a_una_persona_de_paso_ofrece_los_cupos_de_su_dl(): void
    {
        $this->plazasInstaladas(true);
        $ex = new PersonaEx();
        $ex->setId_tabla('pn');
        $ex->setDl('dlz');
        $ex->setApellido1('Paso');
        $this->personas[-1005] = $ex;
        $this->asistencia(-1005, 'dlb>dlz');

        $r = $this->uc()->build(['id_nom' => '-1005', 'id_activ' => self::ID_ACTIV]);

        $this->assertSame(['paso' => 'dlz'], $r['propietario_opciones']);
        $this->assertSame('dlb>dlz', $r['propietario_selected']);
    }

    public function test_una_persona_que_no_se_encuentra_devuelve_error(): void
    {
        $r = $this->uc()->build(['id_nom' => '10019', 'id_activ' => self::ID_ACTIV]);

        $this->assertStringContainsString('10019', $r['error']);
    }
}
