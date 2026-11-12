<?php

namespace Tests\unit\encargossacd\application\services;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\encargossacd\domain\value_objects\LocaleCode;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;

/**
 * Unitarios de {@see EncargoAplicacionService}.
 *
 * Se cubren las partes unitariables sin BD: caching de traducciones,
 * texto de dedicaciones (con m/t/v y plural/singular), seccion segun
 * permisos de sesion, lugar segun centros existentes, y la mutacion
 * `crear_encargo`.
 *
 * Los metodos que hacen `echo` de error (insert/modificar/finalizar)
 * se cubrirían mejor con tests de integracion por el efecto secundario;
 * aqui se testea la coordinacion basica via spy-repo.
 */
final class EncargoAplicacionServiceTest extends TestCase
{
    private EncargoAplicacionService $service;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->makeService();
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    // ============================================================
    // getArrayTraducciones
    // ============================================================

    public function test_getArrayTraducciones_devuelve_textos_del_idioma_solicitado(): void
    {
        $textos = [
            $this->textoStub('t_mañana', 'es_ES.UTF-8', 'mañana'),
            $this->textoStub('t_mañanas', 'es_ES.UTF-8', 'mañanas'),
            $this->textoStub('t_mañana', 'en_US.UTF-8', 'morning'),
        ];
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())->method('matching')->willReturn($textos);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $rta = $this->service->getArrayTraducciones('en_US.UTF-8');

        $this->assertSame(['t_mañana' => 'morning'], $rta);
    }

    public function test_getArrayTraducciones_cachea_y_no_vuelve_a_consultar_el_repo(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())->method('matching')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $this->service->getArrayTraducciones('es_ES.UTF-8');
        $this->service->getArrayTraducciones('es_ES.UTF-8');
        // Segunda llamada resuelve desde `$this->a_txt` sin consultar el repo.
    }

    public function test_getArrayTraducciones_idioma_vacio_usa_es_ES_UTF8(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $this->assertSame(['k' => 'v'], $this->service->getArrayTraducciones(''));
    }

    public function test_getArrayTraducciones_idioma_sin_textos_devuelve_mensaje_de_error(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $rta = $this->service->getArrayTraducciones('fr_FR.UTF-8');

        $this->assertSame('No existe text para el idioma: fr_FR.UTF-8', $rta);
    }

    // ============================================================
    // getTraduccion
    // ============================================================

    public function test_getTraduccion_devuelve_texto_del_idioma_pedido(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('hola', 'es_ES.UTF-8', 'hola'),
            $this->textoStub('hola', 'en_US.UTF-8', 'hello'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $this->assertSame('hello', $this->service->getTraduccion('hola', 'en_US.UTF-8'));
    }

    public function test_getTraduccion_cae_al_es_ES_UTF8_si_falta_en_el_idioma_pedido(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('hola', 'es_ES.UTF-8', 'hola-es'),
            $this->textoStub('otra', 'en_US.UTF-8', 'other'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        // `hola` no existe en en_US -> fallback a es_ES.UTF-8.
        $this->assertSame('hola-es', $this->service->getTraduccion('hola', 'en_US.UTF-8'));
    }

    public function test_getTraduccion_clave_inexistente_devuelve_cadena_vacia_y_emite_mensaje(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('otra', 'es_ES.UTF-8', 'v'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        ob_start();
        $rta = $this->service->getTraduccion('inexistente', 'en_US.UTF-8');
        $output = ob_get_clean();

        $this->assertSame('', $rta);
        $this->assertStringContainsString('inexistente', $output);
        $this->assertStringContainsString('en_US.UTF-8', $output);
    }

    // ============================================================
    // getArraySeccion
    // ============================================================

    public function test_getArraySeccion_con_permiso_des_incluye_sf(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub(['des' => true]);

        $arr = $this->service->getArraySeccion();

        $this->assertArrayHasKey('2', $arr);
        $this->assertSame('sf', $arr['2']);
    }

    public function test_getArraySeccion_con_permiso_vcsd_incluye_sf(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub(['vcsd' => true]);

        $this->assertSame('sf', $this->service->getArraySeccion()['2']);
    }

    public function test_getArraySeccion_sin_permisos_no_incluye_sf(): void
    {
        $_SESSION['oPerm'] = $this->oPermStub([]);

        $arr = $this->service->getArraySeccion();

        $this->assertArrayNotHasKey('2', $arr);
        // El resto de claves siguen presentes (PHP castea a int las numericas).
        $this->assertSame([1, 3, 4, 5, 8], array_keys($arr));
    }

    // ============================================================
    // getTxtDedicacion
    // ============================================================

    public function test_getTxtDedicacion_sin_horarios_devuelve_cadena_vacia(): void
    {
        // Ningun case entra -> "(, , )" -> limpiezas -> "(, )" -> "()" -> "".
        $this->assertSame('', $this->service->getTxtDedicacion([]));
    }

    public function test_getTxtDedicacion_con_manyana_pluraliza_con_dia_inc_mayor_que_uno(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('t_mañana', 'es_ES.UTF-8', 'mañanas'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        // `dia_inc > 1` -> rama `t_mañana` (texto "mañanas" en el fixture).
        $txt = $this->service->getTxtDedicacion([$this->horarioStub('m', 3)]);
        $this->assertSame('(3 mañanas)', $txt);
    }

    public function test_getTxtDedicacion_con_manyana_singular_cuando_dia_inc_es_uno(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('t_mañanas', 'es_ES.UTF-8', 'mañana'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        // `dia_inc === 1` -> rama `t_mañanas` (texto "mañana" en el fixture).
        $txt = $this->service->getTxtDedicacion([$this->horarioStub('m', 1)]);
        $this->assertSame('(1 mañana)', $txt);
    }

    public function test_getTxtDedicacion_combina_m_t_v(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('t_mañana', 'es_ES.UTF-8', 'mañanas'),
            $this->textoStub('t_tarde1', 'es_ES.UTF-8', 'tardes'),
            $this->textoStub('t_tarde2', 'es_ES.UTF-8', 'noches'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        $txt = $this->service->getTxtDedicacion([
            $this->horarioStub('m', 2),
            $this->horarioStub('t', 3),
            $this->horarioStub('v', 4),
        ]);
        $this->assertSame('(2 mañanas, 3 tardes, 4 noches)', $txt);
    }

    public function test_getTxtDedicacion_solo_con_t_limpia_separadores_sobrantes(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            $this->textoStub('t_tarde1', 'es_ES.UTF-8', 'tardes'),
        ]);
        $this->service = $this->makeService([EncargoTextoRepositoryInterface::class => $repo]);

        // Solo `t` -> las sustituciones de comas deben colapsar "(, 3 tardes, )"
        // hasta "(3 tardes)".
        $txt = $this->service->getTxtDedicacion([$this->horarioStub('t', 3)]);
        $this->assertSame('(3 tardes)', $txt);
    }

    // ============================================================
    // dedicacion_ctr / dedicacion
    // ============================================================

    public function test_dedicacion_ctr_sin_horarios_devuelve_false(): void
    {
        $horarioRepo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $horarioRepo->method('matching')->willReturn([]);
        $this->service = $this->makeService([
            EncargoHorarioRepositoryInterface::class => $horarioRepo,
        ]);

        $this->assertFalse($this->service->dedicacion_ctr(10, 20));
    }

    public function test_dedicacion_sin_horarios_de_sacd_devuelve_false(): void
    {
        $sacdHorarioRepo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $sacdHorarioRepo->method('matching')->willReturn([]);
        $this->service = $this->makeService([
            EncargoSacdHorarioRepositoryInterface::class => $sacdHorarioRepo,
        ]);

        $this->assertFalse($this->service->dedicacion(1, 2));
    }

    // ============================================================
    // getLugar_dl
    // ============================================================

    public function test_getLugar_dl_sin_dl_ni_cr_devuelve_interrogacion(): void
    {
        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('matching')->willReturn([]);
        $this->service = $this->makeService([
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $this->assertSame('?', $this->service->getLugar_dl());
    }

    public function test_getLugar_dl_con_una_dl_devuelve_poblacion_de_su_direccion(): void
    {
        $oDireccion = new class {
            public function getPoblacion(): string { return 'Barcelona'; }
        };
        $oCentro = new class($oDireccion) {
            public function __construct(private readonly object $dir) {}
            public function getDirecciones(): array { return [$this->dir]; }
        };

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('matching')
            ->with(Criteria::create()->where(Filter::eq(CentroDlField::TipoCtr, 'dl')))
            ->willReturn([$oCentro]);
        $this->service = $this->makeService([
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $this->assertSame('Barcelona', $this->service->getLugar_dl());
    }

    public function test_getLugar_dl_con_varias_direcciones_las_concatena_con_br(): void
    {
        $oCentro = new class {
            public function getDirecciones(): array
            {
                return [
                    new class { public function getPoblacion(): string { return 'Madrid'; } },
                    new class { public function getPoblacion(): string { return 'Alcala'; } },
                ];
            }
        };

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('matching')->willReturn([$oCentro]);
        $this->service = $this->makeService([
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $this->assertSame('Madrid<br>Alcala', $this->service->getLugar_dl());
    }

    public function test_getLugar_dl_sin_dl_pero_con_cr_usa_cr(): void
    {
        $oCentroCr = new class {
            public function getDirecciones(): array
            {
                return [new class { public function getPoblacion(): string { return 'Region X'; } }];
            }
        };

        $centroRepo = $this->createMock(CentroDlRepositoryInterface::class);
        $centroRepo->method('matching')->willReturnCallback(function (Criteria $criterio) use ($oCentroCr): array {
            return match ($criterio->filters()[0]->value ?? null) {
                'dl' => [],
                'cr' => [$oCentroCr],
                default => [],
            };
        });
        $this->service = $this->makeService([
            CentroDlRepositoryInterface::class => $centroRepo,
        ]);

        $this->assertSame('Region X', $this->service->getLugar_dl());
    }

    // ============================================================
    // crear_encargo
    // ============================================================

    public function test_crear_encargo_pide_new_id_construye_entidad_y_guarda(): void
    {
        $repo = $this->createMock(EncargoRepositoryInterface::class);
        $repo->expects($this->once())->method('getNewId')->willReturn(777);

        $capturedEncargo = null;
        $repo->expects($this->once())
            ->method('Guardar')
            ->willReturnCallback(function (Encargo $oEncargo) use (&$capturedEncargo): bool {
                $capturedEncargo = $oEncargo;
                return true;
            });

        $this->service = $this->makeService([
            EncargoRepositoryInterface::class => $repo,
        ]);

        $returnedId = $this->service->crear_encargo(
            id_tipo_enc: 7000,
            sf_sv: 1,
            id_ubi: 42,
            id_zona: 5,
            desc_enc: 'mi encargo',
            idioma_enc: 'es_ES.UTF-8',
            desc_lugar: 'lugar x',
            observ: 'obs',
        );

        $this->assertSame(777, $returnedId);
        $this->assertNotNull($capturedEncargo);
        $this->assertSame(777, $capturedEncargo->getId_enc());
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function textoStub(string $clave, string $idioma, string $texto): object
    {
        return new class($clave, $idioma, $texto) {
            public function __construct(private readonly string $c, private readonly string $i, private readonly string $t) {}
            public function getClaveVo(): EncargoTextClave { return new EncargoTextClave($this->c); }
            public function getIdiomaVo(): LocaleCode { return new LocaleCode($this->i); }
            public function getTextoVo(): EncargoText { return new EncargoText($this->t); }
        };
    }

    private function horarioStub(string $dia_ref, int $dia_inc): object
    {
        return new class($dia_ref, $dia_inc) {
            public function __construct(private readonly string $r, private readonly int $i) {}
            public function getDia_inc(): int { return $this->i; }
            public function getDiaRefVo(): DiaRefCode { return new DiaRefCode($this->r); }
        };
    }

    /**
     * @param array<string, bool> $perms
     */
    private function oPermStub(array $perms): object
    {
        return new class($perms) {
            /** @param array<string, bool> $perms */
            public function __construct(private readonly array $perms) {}
            public function have_perm_oficina(string $p): bool { return $this->perms[$p] ?? false; }
        };
    }

    /**
     * @param array<class-string, object> $overrides
     */
    // ============================================================
    // horarios del centro (encargo) y del sacd
    // ============================================================

    /** @param list<\src\encargossacd\domain\entity\EncargoHorario> $abiertos @return list<\src\encargossacd\domain\entity\EncargoHorario> guardados */
    private function horariosCtr(array $abiertos, callable $accion): array
    {
        $guardados = [];
        $repo = $this->createMock(EncargoHorarioRepositoryInterface::class);
        $repo->method('matching')->willReturn($abiertos);
        $repo->method('getNewId')->willReturn(900);
        $repo->method('Guardar')->willReturnCallback(function ($h) use (&$guardados): bool {
            $guardados[] = $h;
            return true;
        });
        $accion($this->makeService([EncargoHorarioRepositoryInterface::class => $repo]));
        return $guardados;
    }

    private static function horarioCtr(int $diaInc, string $fIni): \src\encargossacd\domain\entity\EncargoHorario
    {
        $h = new \src\encargossacd\domain\entity\EncargoHorario();
        $h->setId_item_h(10);
        $h->setId_enc(5);
        $h->setDiaRefVo('m');
        $h->setDia_inc($diaInc);
        $h->setN_sacd(1);
        $h->setF_ini(new \src\shared\domain\value_objects\DateTimeLocal($fIni));
        return $h;
    }

    public function test_horario_ctr_nuevo_si_no_habia(): void
    {
        $g = $this->horariosCtr([], fn (EncargoAplicacionService $s) => $s->modificar_horario_ctr(5, 'm', '2', 0));
        $this->assertCount(1, $g);
        $this->assertSame([900, 2, 1, null], [$g[0]->getId_item_h(), $g[0]->getDia_inc(), $g[0]->getN_sacd(), $g[0]->getF_fin()]);
        $this->assertSame([], $this->horariosCtr([], fn (EncargoAplicacionService $s) => $s->modificar_horario_ctr(5, 'm', '', 1)), 'sin dedicación no crea nada');
    }

    public function test_horario_ctr_cambiar_la_dedicacion_cierra_el_anterior_y_abre_uno_nuevo(): void
    {
        $viejo = self::horarioCtr(1, '2020-01-01');
        $g = $this->horariosCtr([$viejo], fn (EncargoAplicacionService $s) => $s->modificar_horario_ctr(5, 'm', '3', 2));
        $this->assertCount(2, $g);
        $this->assertSame($viejo, $g[0]);
        $this->assertSame(1, $viejo->getDia_inc(), 'el anterior conserva su dedicación');
        $this->assertSame(date('Y-m-d'), $viejo->getF_fin()?->format('Y-m-d'), 'y queda cerrado');
        $this->assertSame([3, 2, null], [$g[1]->getDia_inc(), $g[1]->getN_sacd(), $g[1]->getF_fin()], 'un solo horario abierto, con la dedicación nueva');
    }

    public function test_horario_ctr_empezado_hoy_se_modifica_sin_duplicar(): void
    {
        $hoy = self::horarioCtr(1, date('Y-m-d'));
        $g = $this->horariosCtr([$hoy], fn (EncargoAplicacionService $s) => $s->modificar_horario_ctr(5, 'm', '3', 1));
        $this->assertSame([$hoy], $g);
        $this->assertSame([3, null], [$hoy->getDia_inc(), $hoy->getF_fin()]);
    }

    public function test_horario_ctr_misma_dedicacion_solo_cambia_el_numero_de_sacd(): void
    {
        $viejo = self::horarioCtr(2, '2020-01-01');
        $g = $this->horariosCtr([$viejo], fn (EncargoAplicacionService $s) => $s->modificar_horario_ctr(5, 'm', '2', 3));
        $this->assertSame([$viejo], $g);
        $this->assertSame([2, 3, null], [$viejo->getDia_inc(), $viejo->getN_sacd(), $viejo->getF_fin()]);
    }

    /** @param list<object> $existentes @return array{guardados: list<object>, eliminados: list<object>} */
    private function conRepo(string $interfaz, array $existentes, callable $accion): array
    {
        $r = ['guardados' => [], 'eliminados' => []];
        $repo = $this->createMock($interfaz);
        $repo->method('matching')->willReturn($existentes);
        $repo->method('getNewId')->willReturn(901);
        $repo->method('Guardar')->willReturnCallback(function ($o) use (&$r): bool {
            $r['guardados'][] = $o;
            return true;
        });
        $repo->method('Eliminar')->willReturnCallback(function ($o) use (&$r): bool {
            $r['eliminados'][] = $o;
            return true;
        });
        $accion($this->makeService([$interfaz => $repo]));
        return $r;
    }

    private static function horarioSacd(int $diaInc, string $fIni, ?string $fFin = null): \src\encargossacd\domain\entity\EncargoSacdHorario
    {
        $h = new \src\encargossacd\domain\entity\EncargoSacdHorario();
        $h->setId_item(11);
        $h->setId_enc(5);
        $h->setId_nom(3);
        $h->setDiaRefVo('m');
        $h->setDia_inc($diaInc);
        $h->setF_ini(new \src\shared\domain\value_objects\DateTimeLocal($fIni));
        $h->setF_fin($fFin === null ? null : new \src\shared\domain\value_objects\DateTimeLocal($fFin));
        return $h;
    }

    public function test_horario_sacd_cambiar_dedicacion_cierra_y_abre_o_modifica_si_es_de_hoy(): void
    {
        $viejo = self::horarioSacd(1, '2020-01-01');
        $r = $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [$viejo], fn ($s) => $s->modificar_horario_sacd(40, 5, 3, 'm', '2'));
        $this->assertCount(2, $r['guardados']);
        $this->assertSame([1, date('Y-m-d')], [$viejo->getDia_inc(), $viejo->getF_fin()?->format('Y-m-d')]);
        $this->assertSame([2, 40, null], [$r['guardados'][1]->getDia_inc(), $r['guardados'][1]->getId_item_tarea_sacd(), $r['guardados'][1]->getF_fin()]);

        $hoy = self::horarioSacd(1, date('Y-m-d'));
        $r = $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [$hoy], fn ($s) => $s->modificar_horario_sacd(40, 5, 3, 'm', '2'));
        $this->assertSame([$hoy], $r['guardados']);
        $this->assertSame(2, $hoy->getDia_inc());
    }

    public function test_horario_sacd_quitar_la_dedicacion_lo_cierra_y_la_misma_lo_reabre_si_se_cerro_hoy(): void
    {
        $h = self::horarioSacd(2, '2020-01-01');
        $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [$h], fn ($s) => $s->modificar_horario_sacd(40, 5, 3, 'm', ''));
        $this->assertSame([null, date('Y-m-d')], [$h->getDia_inc(), $h->getF_fin()?->format('Y-m-d')]);

        $cerradoHoy = self::horarioSacd(2, '2020-01-01', date('Y-m-d'));
        $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [$cerradoHoy], fn ($s) => $s->modificar_horario_sacd(40, 5, 3, 'm', '2'));
        $this->assertNull($cerradoHoy->getF_fin());

        $r = $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [], fn ($s) => $s->modificar_horario_sacd(40, 5, 3, 'm', '1'));
        $this->assertSame([901, 1], [$r['guardados'][0]->getId_item(), $r['guardados'][0]->getDia_inc()]);
    }

    public function test_finalizar_el_horario_de_un_sacd(): void
    {
        $a = self::horarioSacd(1, '2020-01-01');
        $b = self::horarioSacd(2, '2020-01-01');
        $fin = new \src\shared\domain\value_objects\DateTimeLocal('2026-06-30');
        $r = $this->conRepo(EncargoSacdHorarioRepositoryInterface::class, [$a, $b], fn ($s) => $s->finalizar_horario_sacd(5, 3, $fin));
        $this->assertCount(2, $r['guardados']);
        $this->assertSame(['2026-06-30', '2026-06-30'], [$a->getF_fin()->format('Y-m-d'), $b->getF_fin()->format('Y-m-d')]);
    }

    private static function encargoSacd(string $fIni, ?string $fFin): \src\encargossacd\domain\entity\EncargoSacd
    {
        $e = new \src\encargossacd\domain\entity\EncargoSacd();
        $e->setId_item(12);
        $e->setId_enc(5);
        $e->setId_nom(3);
        $e->setModo(2);
        $e->setF_ini(new \src\shared\domain\value_objects\DateTimeLocal($fIni));
        $e->setF_fin($fFin === null ? null : new \src\shared\domain\value_objects\DateTimeLocal($fFin));
        return $e;
    }

    public function test_asignar_un_sacd_crea_reabre_o_respeta_el_vigente(): void
    {
        $r = $this->conRepo(EncargoSacdRepositoryInterface::class, [], fn ($s) => $s->insert_sacd(5, 3, 2));
        $this->assertSame([901, 5, 3, 2, null], [$r['guardados'][0]->getId_item(), $r['guardados'][0]->getId_enc(), $r['guardados'][0]->getId_nom(), $r['guardados'][0]->getModoVo()->value(), $r['guardados'][0]->getF_fin()]);

        $cerradoHoy = self::encargoSacd('2020-01-01', date('Y-m-d'));
        $r = $this->conRepo(EncargoSacdRepositoryInterface::class, [$cerradoHoy], fn ($s) => $s->insert_sacd(5, 3, 2));
        $this->assertSame([$cerradoHoy], $r['guardados'], 'se reabre el que se cerró hoy');
        $this->assertNull($cerradoHoy->getF_fin());

        $vigente = self::encargoSacd('2020-01-01', null);
        $r = $this->conRepo(EncargoSacdRepositoryInterface::class, [$vigente], fn ($s) => $s->insert_sacd(5, 3, 2));
        $this->assertSame([], $r['guardados'], 'ya lo tiene');

        $antiguo = self::encargoSacd('2010-01-01', '2015-01-01');
        $r = $this->conRepo(EncargoSacdRepositoryInterface::class, [$antiguo], fn ($s) => $s->insert_sacd(5, 3, 2));
        $this->assertCount(1, $r['guardados']);
        $this->assertNotSame($antiguo, $r['guardados'][0], 'uno nuevo si el anterior acabó otro día');
    }

    public function test_finalizar_y_borrar_un_sacd_del_encargo(): void
    {
        $e = self::encargoSacd('2020-01-01', null);
        $fin = new \src\shared\domain\value_objects\DateTimeLocal('2026-06-30');
        $this->conRepo(EncargoSacdRepositoryInterface::class, [$e], fn ($s) => $s->finalizar_sacd(5, 3, 2, $fin));
        $this->assertSame('2026-06-30', $e->getF_fin()->format('Y-m-d'));
        $r = $this->conRepo(EncargoSacdRepositoryInterface::class, [$e], fn ($s) => $s->delete_sacd(5, 3, 2));
        $this->assertSame([$e], $r['eliminados']);
    }

    private function makeService(array $overrides = []): EncargoAplicacionService
    {
        $services = array_merge([
            EncargoTextoRepositoryInterface::class => $this->createStub(EncargoTextoRepositoryInterface::class),
            CentroDlRepositoryInterface::class => $this->createStub(CentroDlRepositoryInterface::class),
            EncargoHorarioRepositoryInterface::class => $this->createStub(EncargoHorarioRepositoryInterface::class),
            EncargoSacdHorarioRepositoryInterface::class => $this->createStub(EncargoSacdHorarioRepositoryInterface::class),
            EncargoSacdRepositoryInterface::class => $this->createStub(EncargoSacdRepositoryInterface::class),
            EncargoRepositoryInterface::class => $this->createStub(EncargoRepositoryInterface::class),
        ], $overrides);

        return new EncargoAplicacionService(
            $services[EncargoTextoRepositoryInterface::class],
            $services[CentroDlRepositoryInterface::class],
            $services[EncargoHorarioRepositoryInterface::class],
            $services[EncargoSacdHorarioRepositoryInterface::class],
            $services[EncargoSacdRepositoryInterface::class],
            $services[EncargoRepositoryInterface::class],
        );
    }
}
