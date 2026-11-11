<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\domain\entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\asistentes\domain\value_objects\AsistenteEncargo;
use src\asistentes\domain\value_objects\AsistenteObserv;
use src\asistentes\domain\value_objects\AsistentePk;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\ubiscamas\domain\value_objects\CamaId;

/**
 * Reglas propias de la entidad `Asistente` que no son un getter/setter: permiso de modificar, tabla según
 * `id_tabla`, clave, comprobación de plaza y metadatos del formulario de dossier.
 */
final class AsistenteReglasTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION ?? [];
        // mi_delef() = «dlb» (esquema H-dlbv, sv).
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv', 'sfsv' => 1];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    private function asistente(): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(3001);
        $a->setId_nom(10011);

        return $a;
    }

    /**
     * @return iterable<string, array{?string, bool}>
     */
    public static function responsablesProvider(): iterable
    {
        yield 'de mi dl' => ['dlb', true];
        yield 'de otra dl' => ['dlp', false];
        yield 'de mi dl pero de sf' => ['dlbf', false];
        yield 'sin dl responsable' => [null, false];
    }

    #[DataProvider('responsablesProvider')]
    public function test_solo_se_puede_modificar_si_el_responsable_es_mi_dl(?string $dl, bool $puede): void
    {
        $a = $this->asistente();
        $a->setDl_responsable($dl);

        $this->assertSame($puede, $a->perm_modificar());
    }

    public function test_en_sf_mi_dl_lleva_la_f(): void
    {
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbf', 'sfsv' => 2];
        $a = $this->asistente();
        $a->setDl_responsable('dlbf');

        $this->assertTrue($a->perm_modificar());
    }

    /**
     * @return iterable<string, array{?string, string}>
     */
    public static function tablasProvider(): iterable
    {
        yield 'dl' => ['dl', 'd_asistentes_dl'];
        yield 'ex' => ['ex', 'd_asistentes_ex'];
        yield 'out' => ['out', 'd_asistentes_out'];
        yield 'otro código: se usa como sufijo' => ['pub', 'd_asistentes_pub'];
        yield 'sin id_tabla' => [null, 'd_asistentes_all'];
    }

    #[DataProvider('tablasProvider')]
    public function test_la_tabla_depende_de_id_tabla(?string $idTabla, string $tabla): void
    {
        $a = $this->asistente();
        $a->setId_tabla($idTabla);

        $this->assertSame($tabla, $a->getNomTabla());
    }

    public function test_clave_primaria(): void
    {
        $a = $this->asistente();

        $this->assertTrue($a->getAsistentePk()->equals(new AsistentePk(3001, 10011)));
        $this->assertSame('pkey', $a->getPrimary_key());
        $this->assertSame(['id_activ' => 3001, 'id_nom' => 10011], $a->getPkey());
    }

    public function test_los_setters_vo_aceptan_el_objeto_o_el_texto(): void
    {
        $a = $this->asistente();
        $encargo = new AsistenteEncargo('cocina');
        $a->setEncargoVo($encargo);
        $a->setObservVo(new AsistenteObserv('llega tarde'));
        $a->setIdTablaVo(new PersonaTablaCode('dl'));
        $cama = new CamaId('cama-7');
        $a->setCamaVo($cama);

        $this->assertSame($encargo, $a->getEncargoVo());
        $this->assertSame('llega tarde', $a->getObserv());
        $this->assertSame('dl', $a->getId_tabla());
        $this->assertSame($cama, $a->getCamaVo());
        $this->assertSame('cama-7', $a->getCama());

        $a->setEncargoVo('  ');
        $a->setIdTablaVo(null);
        $a->setCama(null);
        $this->assertNull($a->getEncargoVo());
        $this->assertNull($a->getIdTablaVo());
        $this->assertNull($a->getCamaVo());
    }

    public function test_un_encargo_demasiado_largo_no_se_acepta(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->asistente()->setEncargo(str_repeat('x', 256));
    }

    public function test_comprobar_plaza_con_un_plaza_id_pasa_la_plaza_actual_y_la_nueva(): void
    {
        $a = $this->asistente();
        $a->setPlaza(PlazaId::EN_ESPERA);
        $comprobador = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $comprobador->expects($this->once())->method('asegurar')
            ->with($a, PlazaId::EN_ESPERA, PlazaId::CONFIRMADA, true)
            ->willReturn('');

        $this->assertSame('', $a->setPlazaVoComprobando(new PlazaId(PlazaId::CONFIRMADA), $comprobador, true));
        $this->assertSame(PlazaId::CONFIRMADA, $a->getPlazaVo()?->value());
    }

    /**
     * Hallazgo: la plaza nueva queda puesta en la entidad aunque el comprobador la rechace; quien llama tiene que
     * descartar el objeto (los casos de uso no lo guardan, pero la entidad queda inconsistente).
     */
    public function test_si_el_comprobador_rechaza_la_plaza_esta_queda_puesta_igualmente(): void
    {
        $a = $this->asistente();
        $comprobador = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $comprobador->method('asegurar')->willReturn('Ya están todas las plazas ocupadas');

        $this->assertNotSame('', $a->setPlazaVoComprobando(PlazaId::ASIGNADA, $comprobador));
        $this->assertSame(PlazaId::ASIGNADA, $a->getPlazaVo()?->value());
    }

    public function test_sin_plaza_previa_la_actual_es_pedida_y_sin_comprobador_no_se_comprueba(): void
    {
        $a = $this->asistente();
        $comprobador = $this->createMock(PlazaPropietarioAsignacionInterface::class);
        $comprobador->expects($this->once())->method('asegurar')->with($a, PlazaId::PEDIDA, PlazaId::ASIGNADA, false)->willReturn('');

        $a->setPlazaComprobando(PlazaId::ASIGNADA, $comprobador);
        $this->assertSame('', $this->asistente()->setPlazaVoComprobando(PlazaId::ASIGNADA));
    }

    public function test_los_datos_del_formulario_de_dossier_describen_los_campos_editables(): void
    {
        $campos = $this->asistente()->getDatosCampos();

        $resumen = array_map(static fn ($c): array => [$c->getNom_camp(), $c->getTipo(), $c->getMetodoGet(), $c->getMetodoSet()], $campos);
        $this->assertSame([
            ['id_nom', 'hidden', 'getId_nom', 'setId_nom'],
            ['id_activ', 'opciones', 'getId_activ', 'setId_activ'],
            ['propio', 'check', 'isPropio', 'setPropio'],
            ['est_ok', 'check', 'isEst_ok', 'setEst_ok'],
            ['falta', 'check', 'isFalta', 'setFalta'],
            ['observ', 'texto', 'getObserv', 'setObserv'],
        ], $resumen);
        // Los métodos declarados existen en la entidad.
        foreach ($resumen as [, , $get, $set]) {
            $this->assertTrue(method_exists(Asistente::class, $get), $get);
            $this->assertTrue(method_exists(Asistente::class, $set), $set);
        }
        $this->assertSame('getNom_activ', $campos[1]->getArgument2());
    }

    public function test_una_clave_desde_array_sin_ids_numericos_se_rechaza(): void
    {
        $this->assertTrue(AsistentePk::fromArray(['id_activ' => '7', 'id_nom' => '-1005'])->equals(new AsistentePk(7, -1005)));

        $this->expectException(\InvalidArgumentException::class);
        AsistentePk::fromArray(['id_activ' => 'x', 'id_nom' => 1]);
    }
}
