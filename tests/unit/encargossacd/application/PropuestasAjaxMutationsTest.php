<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasAjaxMutations;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\security\HashB;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\personas\domain\entity\PersonaSacd;
use Tests\support\DescribeCriteria;

final class PropuestasAjaxMutationsTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousPost;

    protected function setUp(): void
    {
        parent::setUp();
        session_id('test-session-propuestas-ajax');
        $this->previousPost = $_POST;
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_POST = $this->previousPost;
        parent::tearDown();
    }

    public function test_operacion_desconocida_devuelve_error(): void
    {
        $useCase = new PropuestasAjaxMutations(
            $this->createMock(PropuestaEncargoSacdRepositoryInterface::class),
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(EncargoRepositoryInterface::class),
        );

        $out = $useCase->execute('no_existe');

        $this->assertFalse($out['success']);
        $this->assertNotEmpty($out['mensaje']);
    }

    public function test_lista_sacd_returns_unsigned_popup_data(): void
    {
        $_POST = [
            'id_sacd' => '3',
            'id_item' => '4',
            'id_enc' => '5',
            'tipo' => 'titular',
        ];
        $personas = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personas->expects($this->once())
            ->method('getArraySacd')
            ->with("AND id_tabla ~ '^(a|n|sss)$'")
            ->willReturn([3 => 'Sacd de prueba']);
        $useCase = new PropuestasAjaxMutations(
            $this->createMock(PropuestaEncargoSacdRepositoryInterface::class),
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $personas,
            $this->createMock(EncargoRepositoryInterface::class),
        );

        $out = $useCase->execute('lista_sacd');

        $this->assertTrue($out['success']);
        $this->assertSame('lista_sacd', $out['popup']);
        $this->assertSame([3 => 'Sacd de prueba'], $out['opciones']);
        $this->assertSame(3, $out['id_sacd']);
        $this->assertArrayNotHasKey('html', $out);
        $this->assertSame(['id_enc' => 5, 'id_item' => 4], HashB::open($out['ctx_cmb_sacd'], 'propuestas_ajax_cmb_sacd'));
    }

    // ------------------------------------------------------------ ramas con datos

    /** @var list<object> */
    private array $guardados = [];
    /** @var list<object> */
    private array $eliminados = [];
    /** @var list<array{int, int, int}> */
    private array $cambios = [];

    /**
     * @param list<PropuestaEncargoSacd> $propuestas resultado de matching()
     * @param array<int, PropuestaEncargoSacd> $porId
     * @param list<EncargoSacdHorario> $horarios
     */
    private function uc(array $propuestas = [], array $porId = [], array $horarios = []): PropuestasAjaxMutations
    {
        $prop = $this->createMock(PropuestaEncargoSacdRepositoryInterface::class);
        $prop->method('matching')->willReturn($propuestas);
        $prop->method('findById')->willReturnCallback(fn (int $id): ?PropuestaEncargoSacd => $porId[$id] ?? null);
        $prop->method('Guardar')->willReturnCallback(function (PropuestaEncargoSacd $p): bool {
            if (!(new \ReflectionProperty(\src\encargossacd\domain\entity\EncargoSacd::class, 'id_item'))->isInitialized($p)) {
                $p->setId_item(777);
            }
            $this->guardados[] = $p;
            return true;
        });
        $prop->method('Eliminar')->willReturnCallback(function (PropuestaEncargoSacd $p): bool {
            $this->eliminados[] = $p;
            return true;
        });
        $hor = $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class);
        $hor->method('matching')->willReturnCallback(function ($c) use ($horarios): array {
            $f = implode('|', DescribeCriteria::filtros($c));
            return array_values(array_filter($horarios, static fn (EncargoSacdHorario $h): bool => !str_contains($f, 'dia_ref eq') || str_contains($f, 'dia_ref eq ' . $h->getDia_ref())));
        });
        $hor->method('Guardar')->willReturnCallback(function (EncargoSacdHorario $h): bool {
            $this->guardados[] = $h;
            return true;
        });
        $hor->method('cambiarSacd')->willReturnCallback(function (int $enc, int $old, int $new): bool {
            $this->cambios[] = [$enc, $old, $new];
            return true;
        });
        $personas = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personas->method('findById')->willReturnCallback(static function (int $id): ?PersonaSacd {
            if ($id !== 3) {
                return null;
            }
            $p = new PersonaSacd();
            $p->setNom('Juan');
            $p->setApellido1('García');
            return $p;
        });
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('findById')->willReturnCallback(static function (int $id): ?Encargo {
            $e = new Encargo();
            $e->setDesc_enc("encargo $id");
            return $e;
        });
        return new PropuestasAjaxMutations($prop, $hor, $personas, $encargos);
    }

    private static function propuesta(int $idItem, int $idNom, ?int $idNomNew): PropuestaEncargoSacd
    {
        $p = new PropuestaEncargoSacd();
        $p->setId_item($idItem);
        $p->setId_enc(5);
        $p->setId_nom($idNom);
        $p->setId_nom_new($idNomNew);
        return $p;
    }

    private static function horario(string $dia, int $inc): EncargoSacdHorario
    {
        $h = new EncargoSacdHorario();
        $h->setId_enc(5);
        $h->setId_nom(3);
        $h->setDia_ref($dia);
        $h->setDia_inc($inc);
        return $h;
    }

    public function test_dedicacion_lee_los_tres_modulos(): void
    {
        $_POST = ['id_sacd' => '3', 'id_item' => '40', 'id_enc' => '5'];
        $out = $this->uc([], [], [self::horario('m', 2), self::horario('v', 1)])->execute('dedicacion');
        $this->assertSame(['2', '', '1'], [$out['dedic_m'], $out['dedic_t'], $out['dedic_v']]);
        $this->assertSame('encargo 5', $out['desc_enc']);
        $this->assertStringContainsString('García', $out['apellidos_nombre']);
        $this->assertSame(['id_enc' => 5, 'id_item' => 40], HashB::open($out['ctx_dedicacion_update'], 'propuestas_ajax_dedicacion_update'));
    }

    public function test_guardar_dedicacion_actualiza_los_existentes_y_crea_los_que_faltan(): void
    {
        $_POST = ['id_sacd' => '3', 'id_item' => '40', 'id_enc' => '5', 'dedic_m' => '3', 'dedic_t' => '0', 'dedic_v' => '2'];
        $m = self::horario('m', 1);
        $out = $this->uc([], [], [$m])->execute('dedicacion_update');
        $this->assertSame(['success' => true], $out);
        $this->assertCount(3, $this->guardados);
        $this->assertSame($m, $this->guardados[0], 'el de la mañana ya existía');
        $this->assertSame(['m' => 3, 't' => 0, 'v' => 2], array_combine(
            array_map(static fn (EncargoSacdHorario $h): string => $h->getDia_ref(), $this->guardados),
            array_map(static fn (EncargoSacdHorario $h): ?int => $h->getDia_inc(), $this->guardados),
        ));
        $this->assertSame(40, $this->guardados[1]->getId_item_tarea_sacd());
    }

    public function test_guardar_dedicacion_de_un_colaborador_nuevo_busca_su_propuesta(): void
    {
        $_POST = ['id_sacd' => '3', 'id_item' => '5', 'id_enc' => '5', 'dedic_m' => '1'];
        $out = $this->uc([self::propuesta(41, 0, 3)])->execute('dedicacion_update');
        $this->assertTrue($out['success']);
        $this->assertSame(41, $this->guardados[0]->getId_item_tarea_sacd());

        $out = $this->uc([])->execute('dedicacion_update');
        $this->assertSame([false, 'No se puede guardar. Vuelva a cargar la vista'], [$out['success'], $out['mensaje']]);
    }

    public function test_info_lista_los_encargos_propuestos_del_sacd(): void
    {
        $_POST = ['id_sacd' => '3'];
        $out = $this->uc([self::propuesta(41, 0, 3), self::propuesta(42, 3, 3)])->execute('info');
        $this->assertSame(['encargo 5', 'encargo 5'], $out['encargos']);
    }

    public function test_cambiar_sacd_de_una_propuesta_existente_mueve_su_horario(): void
    {
        $_POST = ['tipo' => 'titular', 'id_item' => '41', 'id_enc' => '5', 'id_sacd' => '3'];
        $p = self::propuesta(41, 8, null);
        $out = $this->uc([], [41 => $p])->execute('cmb_sacd');
        $this->assertTrue($out['success']);
        $this->assertSame(3, $p->getId_nom_new());
        $this->assertSame([[5, 8, 3]], $this->cambios, 'del titular actual al nuevo');
        $this->assertNull($out['row']);
    }

    public function test_nuevo_colaborador_crea_la_propuesta(): void
    {
        $_POST = ['tipo' => 'colaborador', 'id_item' => '5', 'id_enc' => '5', 'id_sacd' => '3'];
        $out = $this->uc()->execute('cmb_sacd');
        $this->assertTrue($out['success']);
        $nueva = $this->guardados[0];
        $this->assertSame([5, 0, 3, 5], [$nueva->getId_enc(), $nueva->getId_nom(), $nueva->getId_nom_new(), $nueva->getModoVo()->value()]);
        $this->assertSame(['tipo' => 'fila_colaborador', 'id_item' => 777, 'id_enc' => 5, 'id_sacd' => 3, 'nombre' => $out['nombre']], $out['row']);
        $this->assertSame([], $this->cambios);
    }

    public function test_quitar_un_colaborador_propuesto_sin_sacd_lo_borra(): void
    {
        $_POST = ['tipo' => 'colaborador', 'id_item' => '41', 'id_enc' => '5', 'id_sacd' => '0'];
        $p = self::propuesta(41, 0, 3);
        $out = $this->uc([], [41 => $p])->execute('cmb_sacd');
        $this->assertSame(['tipo' => 'borrar'], $out['row']);
        $this->assertSame([$p], $this->eliminados);
        $this->assertSame([[5, 3, 0]], $this->cambios);
    }

    public function test_quitar_el_titular_propuesto_deja_la_celda_vacia(): void
    {
        $_POST = ['tipo' => 'titular', 'id_item' => '41', 'id_enc' => '5', 'id_sacd' => '0'];
        $p = self::propuesta(41, 0, 3);
        $out = $this->uc([], [41 => $p])->execute('cmb_sacd');
        $this->assertSame('celdas', $out['row']['tipo']);
        $this->assertNull($p->getId_nom_new());
        $this->assertSame('nuevo', $out['nombre']);
    }

    public function test_cambiar_sacd_de_una_propuesta_inexistente(): void
    {
        $_POST = ['tipo' => 'titular', 'id_item' => '99', 'id_enc' => '5', 'id_sacd' => '3'];
        $this->assertSame(['success' => false, 'mensaje' => 'Registro no encontrado'], $this->uc()->execute('cmb_sacd'));
    }
}
