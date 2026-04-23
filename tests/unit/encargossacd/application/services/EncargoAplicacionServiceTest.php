<?php

namespace Tests\unit\encargossacd\application\services;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTextoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\encargossacd\domain\value_objects\LocaleCode;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

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
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EncargoAplicacionService();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
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
        $repo->expects($this->once())->method('getEncargoTextos')->willReturn($textos);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        $rta = $this->service->getArrayTraducciones('en_US.UTF-8');

        $this->assertSame(['t_mañana' => 'morning'], $rta);
    }

    public function test_getArrayTraducciones_cachea_y_no_vuelve_a_consultar_el_repo(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->expects($this->once())->method('getEncargoTextos')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        $this->service->getArrayTraducciones('es_ES.UTF-8');
        $this->service->getArrayTraducciones('es_ES.UTF-8');
        // Segunda llamada resuelve desde `$this->a_txt` sin consultar el repo.
    }

    public function test_getArrayTraducciones_idioma_vacio_usa_es_ES_UTF8(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        $this->assertSame(['k' => 'v'], $this->service->getArrayTraducciones(''));
    }

    public function test_getArrayTraducciones_idioma_sin_textos_devuelve_mensaje_de_error(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('k', 'es_ES.UTF-8', 'v'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        $rta = $this->service->getArrayTraducciones('fr_FR.UTF-8');

        $this->assertSame('No existe text para el idioma: fr_FR.UTF-8', $rta);
    }

    // ============================================================
    // getTraduccion
    // ============================================================

    public function test_getTraduccion_devuelve_texto_del_idioma_pedido(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('hola', 'es_ES.UTF-8', 'hola'),
            $this->textoStub('hola', 'en_US.UTF-8', 'hello'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        $this->assertSame('hello', $this->service->getTraduccion('hola', 'en_US.UTF-8'));
    }

    public function test_getTraduccion_cae_al_es_ES_UTF8_si_falta_en_el_idioma_pedido(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('hola', 'es_ES.UTF-8', 'hola-es'),
            $this->textoStub('otra', 'en_US.UTF-8', 'other'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        // `hola` no existe en en_US -> fallback a es_ES.UTF-8.
        $this->assertSame('hola-es', $this->service->getTraduccion('hola', 'en_US.UTF-8'));
    }

    public function test_getTraduccion_clave_inexistente_devuelve_cadena_vacia_y_emite_mensaje(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('otra', 'es_ES.UTF-8', 'v'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

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
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('t_mañana', 'es_ES.UTF-8', 'mañanas'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        // `dia_inc > 1` -> rama `t_mañana` (texto "mañanas" en el fixture).
        $txt = $this->service->getTxtDedicacion([$this->horarioStub('m', 3)]);
        $this->assertSame('(3 mañanas)', $txt);
    }

    public function test_getTxtDedicacion_con_manyana_singular_cuando_dia_inc_es_uno(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('t_mañanas', 'es_ES.UTF-8', 'mañana'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

        // `dia_inc === 1` -> rama `t_mañanas` (texto "mañana" en el fixture).
        $txt = $this->service->getTxtDedicacion([$this->horarioStub('m', 1)]);
        $this->assertSame('(1 mañana)', $txt);
    }

    public function test_getTxtDedicacion_combina_m_t_v(): void
    {
        $repo = $this->createMock(EncargoTextoRepositoryInterface::class);
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('t_mañana', 'es_ES.UTF-8', 'mañanas'),
            $this->textoStub('t_tarde1', 'es_ES.UTF-8', 'tardes'),
            $this->textoStub('t_tarde2', 'es_ES.UTF-8', 'noches'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

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
        $repo->method('getEncargoTextos')->willReturn([
            $this->textoStub('t_tarde1', 'es_ES.UTF-8', 'tardes'),
        ]);
        $GLOBALS['container'] = $this->containerFromMap([EncargoTextoRepositoryInterface::class => $repo]);

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
        $horarioRepo->method('getEncargoHorarios')->willReturn([]);
        $GLOBALS['container'] = $this->containerFromMap([
            EncargoHorarioRepositoryInterface::class => $horarioRepo,
        ]);

        $this->assertFalse($this->service->dedicacion_ctr(10, 20));
    }

    public function test_dedicacion_sin_horarios_de_sacd_devuelve_false(): void
    {
        $sacdHorarioRepo = $this->createMock(EncargoSacdHorarioRepositoryInterface::class);
        $sacdHorarioRepo->method('getEncargoSacdHorarios')->willReturn([]);
        $GLOBALS['container'] = $this->containerFromMap([
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
        $centroRepo->method('getCentros')->willReturn([]);
        $GLOBALS['container'] = $this->containerFromMap([
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
        $centroRepo->method('getCentros')->with(['tipo_ctr' => 'dl'])->willReturn([$oCentro]);
        $GLOBALS['container'] = $this->containerFromMap([
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
        $centroRepo->method('getCentros')->willReturn([$oCentro]);
        $GLOBALS['container'] = $this->containerFromMap([
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
        $centroRepo->method('getCentros')->willReturnCallback(function (array $aWhere) use ($oCentroCr): array {
            return match ($aWhere['tipo_ctr'] ?? null) {
                'dl' => [],
                'cr' => [$oCentroCr],
                default => [],
            };
        });
        $GLOBALS['container'] = $this->containerFromMap([
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

        $GLOBALS['container'] = $this->containerFromMap([
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
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
