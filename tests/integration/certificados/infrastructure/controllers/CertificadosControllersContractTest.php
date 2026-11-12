<?php

declare(strict_types=1);

namespace Tests\integration\certificados\infrastructure\controllers;

use PHPUnit\Framework\MockObject\MockObject;
use src\certificados\domain\CertificadoEmitidoDelete;
use src\certificados\domain\CertificadoEmitidoEnviar;
use src\certificados\domain\CertificadoEmitidoSelect;
use src\certificados\domain\CertificadoRecibidoDelete;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\certificados\domain\entity\CertificadoRecibido;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\InProcessSrcDispatch;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Contrato de los controladores HTTP de `certificados`. Varios llevan lógica de negocio (guardar emitido/recibido,
 * ver): se prueban tal cual, en proceso, con los repositorios simulados, para que el comportamiento quede fijado
 * antes de llevar esa lógica a `application/`.
 */
final class CertificadosControllersContractTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private const DIR = __DIR__ . '/../../../../../src/certificados/infrastructure/ui/http/controllers/';

    private CertificadoEmitidoRepositoryInterface&MockObject $emitidos;
    private CertificadoRecibidoRepositoryInterface&MockObject $recibidos;
    /** @var array<int, PersonaDl> */
    private array $personas = [];
    /** @var array<string, mixed> */
    private array $postAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->postAnterior = $_POST;
        $this->emitidos = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $this->recibidos = $this->createMock(CertificadoRecibidoRepositoryInterface::class);
        $this->sustituirServicio(CertificadoEmitidoRepositoryInterface::class, $this->emitidos);
        $this->sustituirServicio(CertificadoRecibidoRepositoryInterface::class, $this->recibidos);
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(fn (int $id): ?PersonaDl => $this->personas[$id] ?? null);
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_POST = $this->postAnterior;
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function ejecutar(string $controlador, array $post): array
    {
        $_POST = $post;
        InProcessSrcDispatch::begin();
        ob_start();
        try {
            require self::DIR . $controlador;
        } finally {
            $salida = (string) ob_get_clean();
            InProcessSrcDispatch::end();
        }
        $json = json_decode($salida, true);
        $this->assertIsArray($json, "$controlador debe emitir solo JSON; emitió: " . substr($salida, 0, 200));

        return $json;
    }

    private function persona(int $id, string $nombre): void
    {
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidos')->willReturn($nombre);
        $p->method('getApellidosNombre')->willReturn($nombre);
        $this->personas[$id] = $p;
    }

    /** @return array<string, string> */
    private static function formulario(): array
    {
        return [
            'nom' => '',
            'idioma' => 'ca_ES.UTF-8',
            'destino' => 'crGalbel',
            'certificado' => 'H 12/26',
            'firmado' => 't',
            'f_certificado' => '24/7/2026',
        ];
    }

    // ------------------------------------------------------------------ guardar emitido

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function mutacionesConTokenProvider(): iterable
    {
        yield 'guardar emitido' => ['certificado_emitido_guardar.php', 'ctx_guardar'];
        yield 'guardar recibido' => ['certificado_recibido_guardar.php', 'ctx_guardar'];
        yield 'borrar emitido' => ['certificado_emitido_delete.php', 'ctx_eliminar'];
        yield 'borrar recibido' => ['certificado_recibido_delete.php', 'ctx_eliminar'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('mutacionesConTokenProvider')]
    public function test_sin_token_valido_no_se_hace_nada(string $controlador, string $campo): void
    {
        $this->emitidos->expects($this->never())->method('Guardar');
        $this->recibidos->expects($this->never())->method('Guardar');

        $json = $this->ejecutar($controlador, [$campo => 'manipulado', 'id_item' => '7']);

        $this->assertFalse($json['success']);
        $this->assertSame('none', $json['data']);
    }

    public function test_un_emitido_nuevo_toma_id_y_nombre_y_se_guarda_con_mi_region_como_emisora(): void
    {
        $this->persona(10011, 'Joan Pla');
        $this->emitidos->method('getNewId_item')->willReturn(55);
        $guardado = null;
        $this->emitidos->expects($this->once())->method('Guardar')->willReturnCallback(function (CertificadoEmitido $c) use (&$guardado): bool {
            $guardado = $c;

            return true;
        });

        $json = $this->ejecutar('certificado_emitido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10011]),
        ] + self::formulario());

        $this->assertTrue($json['success']);
        $this->assertSame(['mensaje' => 'ok', 'item' => 55], json_decode($json['data'], true));
        $this->assertSame([55, 10011, 'Joan Pla', 'ca_ES.UTF-8', 'crGalbel', 'H 12/26', true], [
            $guardado->getId_item(), $guardado->getId_nom(), $guardado->getNom(), $guardado->getIdiomaVo()?->value(),
            $guardado->getDestino(), $guardado->getCertificado(), $guardado->isFirmado(),
        ]);
        $this->assertSame(ConfigGlobal::mi_region_dl(), $guardado->getEsquema_emisor());
        $this->assertSame('2026-07-24', $guardado->getF_certificado()?->getIso());
        $this->assertNull($guardado->getF_enviado());
    }

    public function test_editar_un_emitido_conserva_el_nombre_escrito_y_la_fecha_de_envio(): void
    {
        $existente = new CertificadoEmitido();
        $existente->setId_item(7);
        $this->emitidos->method('findById')->with(7)->willReturn($existente);
        $this->emitidos->expects($this->never())->method('getNewId_item');
        $this->emitidos->method('Guardar')->willReturn(true);

        $json = $this->ejecutar('certificado_emitido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 0, 'id_item' => 7, 'id_nom' => 10011]),
            'nom' => 'Nombre escrito',
            'f_enviado' => '30/7/2026',
            'idioma' => '',
        ] + self::formulario());

        $this->assertTrue($json['success']);
        $this->assertSame('Nombre escrito', $existente->getNom());
        $this->assertSame('2026-07-30', $existente->getF_enviado()?->getIso());
        $this->assertNull($existente->getIdiomaVo());
    }

    public function test_guardar_un_emitido_que_no_existe_o_de_una_persona_que_no_se_encuentra(): void
    {
        $this->emitidos->method('findById')->willReturn(null);
        $json = $this->ejecutar('certificado_emitido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 0, 'id_item' => 7, 'id_nom' => 10011]),
        ] + self::formulario());
        $this->assertFalse($json['success']);

        $this->emitidos->method('getNewId_item')->willReturn(55);
        $this->emitidos->expects($this->never())->method('Guardar');
        $json = $this->ejecutar('certificado_emitido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10019]),
        ] + self::formulario());
        $this->assertStringContainsString('10019', $json['mensaje']);
    }

    public function test_un_duplicado_al_guardar_se_explica_y_otro_error_se_devuelve_tal_cual(): void
    {
        $this->persona(10011, 'Joan Pla');
        $this->emitidos->method('getNewId_item')->willReturn(55);
        $this->emitidos->method('Guardar')->willThrowException(new \PDOException('duplicate key value violates unique constraint "e_certificados_rstgr_ukey"'));
        $post = ['ctx_guardar' => HashB::sign('certificado_emitido_guardar', ['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10011])] + self::formulario();

        $this->assertStringContainsString('misma fecha', $this->ejecutar('certificado_emitido_guardar.php', $post)['mensaje']);

        $emitidos = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $emitidos->method('getNewId_item')->willReturn(55);
        $emitidos->method('Guardar')->willReturn(false);
        $emitidos->method('getErrorTxt')->willReturn('fallo de conexión');
        $this->sustituirServicio(CertificadoEmitidoRepositoryInterface::class, $emitidos);
        $this->assertSame('fallo de conexión', $this->ejecutar('certificado_emitido_guardar.php', $post)['mensaje']);
    }

    // ------------------------------------------------------------------ guardar recibido

    public function test_un_recibido_nuevo_se_guarda_con_sus_datos(): void
    {
        $this->persona(10011, 'Joan Pla');
        $this->recibidos->method('getNewId_item')->willReturn(31);
        $guardado = null;
        $this->recibidos->expects($this->once())->method('Guardar')->willReturnCallback(function (CertificadoRecibido $c) use (&$guardado): bool {
            $guardado = $c;

            return true;
        });

        $json = $this->ejecutar('certificado_recibido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_recibido_guardar', ['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10011]),
            'f_recibido' => '1/8/2026',
        ] + self::formulario());

        $this->assertTrue($json['success']);
        $this->assertSame(['mensaje' => 'ok', 'item' => 31], json_decode($json['data'], true));
        $this->assertSame([31, 'Joan Pla', 'H 12/26', '2026-08-01'], [$guardado->getId_item(), $guardado->getNom(), $guardado->getCertificado(), $guardado->getF_recibido()?->getIso()]);
    }

    /**
     * Regresión: al modificar un recibido se sobrescribía su esquema emisor con mi dl y se perdía quién lo emitió
     * (lo copia `Trasladar` del certificado emitido).
     */
    public function test_modificar_un_recibido_conserva_la_region_que_lo_emitio(): void
    {
        $existente = new CertificadoRecibido();
        $existente->setId_item(31);
        $existente->setEsquema_emisor('Galbel-Galbelv');
        $existente->setF_recibido(new DateTimeLocal('2026-03-10'));
        $this->recibidos->method('findById')->with(31)->willReturn($existente);
        $this->recibidos->method('Guardar')->willReturn(true);

        $json = $this->ejecutar('certificado_recibido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_recibido_guardar', ['nuevo' => 0, 'id_item' => 31, 'id_nom' => 10011]),
            'nom' => 'Joan Pla',
        ] + self::formulario());

        $this->assertTrue($json['success']);
        $this->assertSame('Galbel-Galbelv', $existente->getEsquema_emisor());
        $this->assertSame('2026-03-10', $existente->getF_recibido()?->getIso(), 'sin fecha en el formulario se conserva la que tenía');
    }

    public function test_si_no_se_guarda_el_recibido_se_devuelve_el_error_del_repositorio(): void
    {
        $this->recibidos->method('getNewId_item')->willReturn(31);
        $this->recibidos->method('Guardar')->willReturn(false);
        $this->recibidos->method('getErrorTxt')->willReturn('fallo de conexión');

        $json = $this->ejecutar('certificado_recibido_guardar.php', [
            'ctx_guardar' => HashB::sign('certificado_recibido_guardar', ['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10011]),
            'nom' => 'Joan Pla',
        ] + self::formulario());

        $this->assertSame('fallo de conexión', $json['mensaje']);
    }

    // ------------------------------------------------------------------ borrar, enviar, listado, ver, locales

    /** Doble de caso de uso con el método que llame el controlador. */
    private function doble(mixed $resultado): object
    {
        return new class($resultado) {
            /** @var list<mixed> */
            public array $llamadas = [];

            public function __construct(private mixed $resultado)
            {
            }

            public function __call(string $metodo, array $args): mixed
            {
                $this->llamadas[] = [$metodo, ...$args];

                return $this->resultado;
            }
        };
    }

    public function test_borrar_pasa_el_id_del_token(): void
    {
        foreach ([[CertificadoEmitidoDelete::class, 'certificado_emitido_delete'], [CertificadoRecibidoDelete::class, 'certificado_recibido_delete']] as [$caso, $nombre]) {
            $doble = $this->doble('');
            $this->sustituirServicio($caso, $doble);

            $json = $this->ejecutar("$nombre.php", ['ctx_eliminar' => HashB::sign($nombre, ['id_item' => 7]), 'id_item' => '99']);

            $this->assertSame(['success' => true, 'data' => 'ok'], $json);
            $this->assertSame([['delete', 7]], $doble->llamadas, 'el id del token, no el del formulario');
        }
    }

    public function test_enviar_toma_el_id_de_la_seleccion_o_del_formulario(): void
    {
        $doble = $this->doble('Hay que enviar manualmente');
        $this->sustituirServicio(CertificadoEmitidoEnviar::class, $doble);

        $json = $this->ejecutar('certificado_emitido_enviar.php', ['sel' => ['7#x']]);
        $this->ejecutar('certificado_emitido_enviar.php', ['id_item' => '8']);

        $this->assertSame(['success' => false, 'mensaje' => 'Hay que enviar manualmente', 'data' => 'ok'], $json);
        $this->assertSame([['execute', 7], ['execute', 8]], $doble->llamadas);
    }

    public function test_el_listado_pasa_el_numero_y_el_curso(): void
    {
        $doble = $this->doble(['success' => true, 'a_valores' => []]);
        $this->sustituirServicio(CertificadoEmitidoSelect::class, $doble);

        $json = $this->ejecutar('certificado_emitido_lista_datos.php', ['certificado' => '12', 'inicurs_ca_iso' => '2025-10-01', 'fincurs_ca_iso' => '2026-09-30']);

        $this->assertTrue($json['success']);
        $this->assertSame([['getCamposVista', '12', '2025-10-01', '2026-09-30']], $doble->llamadas);
    }

    public function test_ver_un_emitido_devuelve_sus_datos_el_pdf_y_el_token_de_guardar(): void
    {
        $c = new CertificadoEmitido();
        $c->setId_item(7);
        $c->setId_nom(10011);
        $c->setCertificado('H 12/26');
        $c->setIdiomaVo('ca_ES.UTF-8');
        $c->setF_certificado(new DateTimeLocal('2026-07-24'));
        $c->setFirmado(true);
        $c->setDocumento('%PDF-1.4');
        $this->emitidos->method('findById')->willReturnMap([[7, $c], [8, null]]);
        $this->persona(10011, 'Pla, Joan');

        $json = $this->ejecutar('certificado_emitido_ver_datos.php', ['id_item' => '7']);
        $d = json_decode($json['data'], true);

        $this->assertSame(['H 12/26', '24/7/2026', 'ca_ES.UTF-8', true, 'Pla, Joan', 'Pla, Joan', 10011], [$d['certificado'], $d['f_certificado'], $d['idioma'], $d['firmado'], $d['nom'], $d['apellidos_nombre'], $d['id_nom']]);
        $this->assertSame('%PDF-1.4', base64_decode($d['content']));
        $this->assertSame(['nuevo' => 0, 'id_item' => 7, 'id_nom' => 10011], HashB::open($d['ctx_guardar'], 'certificado_emitido_guardar'));

        $this->assertStringContainsString('8', $this->ejecutar('certificado_emitido_ver_datos.php', ['id_item' => '8'])['mensaje']);
    }

    public function test_locales_devuelve_la_lista_o_el_error(): void
    {
        $locales = $this->createMock(LocalRepositoryInterface::class);
        $locales->method('getArrayLocales')->willReturn(['ca_ES.UTF-8' => 'català']);
        $this->sustituirServicio(LocalRepositoryInterface::class, $locales);
        $this->assertSame(['a_locales' => ['ca_ES.UTF-8' => 'català']], json_decode($this->ejecutar('certificados_locales_data.php', [])['data'], true));

        $roto = $this->createMock(LocalRepositoryInterface::class);
        $roto->method('getArrayLocales')->willThrowException(new \RuntimeException('sin tabla'));
        $this->sustituirServicio(LocalRepositoryInterface::class, $roto);
        $this->assertSame('sin tabla', $this->ejecutar('certificados_locales_data.php', [])['mensaje']);
    }

    // ------------------------------------------------------------------ guardar el pdf generado, datos para imprimir

    public function test_guardar_pdf_guarda_el_documento_y_apunta_el_certificado_en_las_notas_de_la_region(): void
    {
        $c = new CertificadoEmitido();
        $c->setId_item(7);
        $c->setF_certificado(new DateTimeLocal('2026-07-24'));
        $this->emitidos->method('findById')->willReturnMap([[7, $c], [8, null]]);
        $this->emitidos->expects($this->once())->method('Guardar')->with($c)->willReturn(true);
        $notas = $this->createMock(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class);
        $notas->expects($this->once())->method('addCertificado')->with(10011, 'H 12/26', $this->callback(static fn ($f): bool => $f?->getIso() === '2026-07-24'));
        $this->sustituirDefinicion(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class, $notas);

        $json = $this->ejecutar('certificado_emitido_guardar_pdf.php', ['id_item' => '7', 'id_nom' => '10011', 'certificado' => base64_encode('H 12/26'), 'pdf' => base64_encode('%PDF-1.4')]);

        $this->assertTrue($json['success']);
        $this->assertSame(['mensaje' => 'ok', 'item' => 7], json_decode($json['data'], true));
        $this->assertSame([10011, '%PDF-1.4'], [$c->getId_nom(), $c->getDocumento()]);

        $this->assertStringContainsString('8', $this->ejecutar('certificado_emitido_guardar_pdf.php', ['id_item' => '8'])['mensaje']);
    }

    public function test_guardar_pdf_si_falla_el_guardado_no_apunta_el_certificado(): void
    {
        $c = new CertificadoEmitido();
        $c->setId_item(7);
        $this->emitidos->method('findById')->willReturn($c);
        $this->emitidos->method('Guardar')->willReturn(false);
        $this->emitidos->method('getErrorTxt')->willReturn('fallo de conexión');
        $notas = $this->createMock(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class);
        $notas->expects($this->never())->method('addCertificado');
        $this->sustituirDefinicion(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class, $notas);

        $this->assertSame('fallo de conexión', $this->ejecutar('certificado_emitido_guardar_pdf.php', ['id_item' => '7', 'id_nom' => '10011', 'pdf' => 'no es base64 !!'])['mensaje']);
        $this->assertNull($c->getDocumento(), 'un pdf que no es base64 deja el documento vacío');
    }

    private function config(?string $regionLatin = 'HISPANIAE'): void
    {
        $curso = ['ini_dia' => 1, 'ini_mes' => 10, 'fin_dia' => 30, 'fin_mes' => 9];
        $_SESSION['oConfig'] = new \src\configuracion\domain\value_objects\ConfigSnapshot(
            gesCalendario: null, ceLugar: null, regionLatin: $regionLatin, vstgr: 'Vic. Stgr', lugarFirma: 'Matriti',
            dirStgr: 'D. Stgr', ambito: 'rstgr', notaCorte: null, notaMax: null, caducaCursada: null,
            idiomaDefault: 'es_ES.UTF-8', iniContadorCertificados: '100', jefeCalendario: null, aCursoStgr: $curso, aCursoCrt: $curso,
        );
    }

    public function test_imprimir_avisa_de_los_parametros_de_configuracion_que_faltan(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        try {
            $this->config(null);

            $json = $this->ejecutar('certificado_emitido_imprimir_datos.php', ['id_nom' => '10011']);

            $this->assertFalse($json['success']);
            $this->assertStringContainsString('latín', $json['mensaje']);
        } finally {
            $_SESSION['oConfig'] = $anterior;
        }
    }

    public function test_imprimir_devuelve_los_datos_de_la_persona_y_de_la_region_y_el_token_del_alta(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        try {
            $this->config();
            $p = $this->createMock(PersonaDl::class);
            $p->method('getNombreApellidos')->willReturn('Joan Pla');
            $p->method('getLugar_nacimiento')->willReturn('Reus');
            $p->method('getF_nacimiento')->willReturn(new DateTimeLocal('1990-05-20'));
            $this->personas[10011] = $p;

            $d = json_decode($this->ejecutar('certificado_emitido_imprimir_datos.php', ['id_nom' => '10011'])['data'], true);

            $this->assertSame(['Joan Pla', 'Reus', 'HISPANIAE', 'Vic. Stgr', 'D. Stgr', 'Matriti', '100', date('y')], [
                $d['nombreApellidos'], $d['lugar_nacimiento'], $d['region_latin'], $d['vstgr'], $d['dir_stgr'], $d['lugar_firma'], $d['contador'], $d['any_2digit'],
            ]);
            $this->assertNotSame('', $d['f_nacimiento']);
            $this->assertSame(['nuevo' => 1, 'id_item' => 0, 'id_nom' => 10011], HashB::open($d['ctx_guardar'], 'certificado_emitido_guardar'));

            $this->assertStringContainsString('10019', $this->ejecutar('certificado_emitido_imprimir_datos.php', ['id_nom' => '10019'])['mensaje']);
        } finally {
            $_SESSION['oConfig'] = $anterior;
        }
    }

    // ------------------------------------------------------------------ endpoints de datos de formularios

    /**
     * @return iterable<string, array{string, class-string, string}>
     */
    public static function formulariosProvider(): iterable
    {
        yield 'adjuntar emitido' => ['certificado_emitido_adjuntar_data.php', \src\certificados\application\CertificadoEmitidoAdjuntarFormData::class, 'id_nom'];
        yield 'adjuntar recibido' => ['certificado_recibido_adjuntar_data.php', \src\certificados\application\CertificadoRecibidoAdjuntarFormData::class, 'id_nom'];
        yield 'modificar recibido' => ['certificado_recibido_modificar_data.php', \src\certificados\application\CertificadoRecibidoModificarFormData::class, 'id_item'];
        yield 'subir firmado' => ['certificado_emitido_upload_firmado_data.php', \src\certificados\application\CertificadoEmitidoUploadFirmadoFormData::class, 'id_item'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('formulariosProvider')]
    public function test_los_endpoints_de_formulario_pasan_el_id_y_devuelven_los_datos_o_el_error(string $controlador, string $caso, string $campo): void
    {
        $doble = $this->doble(['nom' => 'Joan Pla']);
        $this->sustituirServicio($caso, $doble);
        $json = $this->ejecutar($controlador, [$campo => '7']);
        $this->assertSame(['nom' => 'Joan Pla'], json_decode($json['data'], true));
        $this->assertSame([['execute', 7]], $doble->llamadas);

        $falla = new class {
            public function execute(int $id): never
            {
                throw new \RuntimeException('error de verdad');
            }
        };
        $this->sustituirServicio($caso, $falla);
        $this->assertSame('error de verdad', $this->ejecutar($controlador, [$campo => '7'])['mensaje']);
    }

    public function test_al_adjuntar_un_aviso_de_configuracion_no_es_un_error(): void
    {
        $avisa = new class {
            public function execute(int $id): never
            {
                throw new \RuntimeException('persona no encontrada');
            }
        };
        $this->sustituirServicio(\src\certificados\application\CertificadoEmitidoAdjuntarFormData::class, $avisa);

        $json = $this->ejecutar('certificado_emitido_adjuntar_data.php', ['id_nom' => '7']);

        $this->assertTrue($json['success']);
        $d = json_decode($json['data'], true);
        $this->assertSame(['persona no encontrada', ''], [$d['aviso'], $d['nom']]);
        $this->assertSame((new DateTimeLocal())->getFromLocal(), $d['f_enviado']);
    }

    // ------------------------------------------------------------------ datos del pdf del certificado (mpdf)

    private function emitidoParaImprimir(string $idioma = ''): void
    {
        $c = new CertificadoEmitido();
        $c->setId_item(7);
        $c->setId_nom(10011);
        $c->setCertificado('H 12/26');
        $c->setIdiomaVo($idioma);
        $c->setF_certificado(new DateTimeLocal('2026-07-24'));
        $c->setFirmado(true);
        $this->emitidos->method('findById')->willReturnMap([[7, $c], [8, null]]);
        $p = $this->createMock(PersonaDl::class);
        $p->method('getApellidosNombre')->willReturn('Pla, Joan');
        $p->method('getLugar_nacimiento')->willReturn('Reus');
        $p->method('getF_nacimiento')->willReturn(new DateTimeLocal('1990-05-20'));
        $this->personas[10011] = $p;
    }

    public function test_pdf_certificado_que_no_existe_o_sin_configuracion(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        try {
            $this->emitidoParaImprimir();
            $this->assertStringContainsString('8', $this->ejecutar('certificado_emitido_imprimir_mpdf_datos.php', ['id_item' => '8'])['mensaje']);

            $this->config(null);
            $this->assertStringContainsString('latín', $this->ejecutar('certificado_emitido_imprimir_mpdf_datos.php', ['id_item' => '7'])['mensaje']);
        } finally {
            $_SESSION['oConfig'] = $anterior;
        }
    }

    public function test_pdf_en_un_idioma_sin_textos_traducidos_avisa(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        try {
            $this->config();
            $this->emitidoParaImprimir('de_DE.UTF-8');

            $json = $this->ejecutar('certificado_emitido_imprimir_mpdf_datos.php', ['id_item' => '7']);

            $this->assertFalse($json['success']);
            $this->assertStringContainsString('de_DE.UTF-8', $json['mensaje']);
        } finally {
            $_SESSION['oConfig'] = $anterior;
        }
    }

    public function test_pdf_en_latin_con_las_asignaturas_aprobadas_de_la_region(): void
    {
        $anterior = $_SESSION['oConfig'] ?? null;
        try {
            $this->config();
            $this->emitidoParaImprimir();
            $asignaturas = $this->createMock(\src\asignaturas\domain\contracts\AsignaturaRepositoryInterface::class);
            $asignaturas->method('getAsignaturasAsJson')->willReturn('[{"id":1101}]');
            $asignaturas->method('findById')->willReturnCallback(function (int $id): ?\src\asignaturas\domain\entity\Asignatura {
                $a = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
                $a->method('isActive')->willReturn($id !== 1102);
                $a->method('getIdNivelVo')->willReturn(new \src\asignaturas\domain\value_objects\NivelId($id));
                $a->method('getCreditos')->willReturn(4.5);
                $a->method('getNombre_asignatura')->willReturn('Asig ' . $id);

                return $a;
            });
            $this->sustituirServicio(\src\asignaturas\domain\contracts\AsignaturaRepositoryInterface::class, $asignaturas);
            $delegaciones = $this->createMock(\src\ubis\domain\contracts\DelegacionRepositoryInterface::class);
            $delegaciones->method('mi_region_stgr')->willReturn(['region_stgr' => 'H']);
            $delegaciones->method('getArrayIdSchemaRegionStgr')->willReturn([1001, 1002]);
            $this->sustituirServicio(\src\ubis\domain\contracts\DelegacionRepositoryInterface::class, $delegaciones);
            $nota = function (int $idAsignatura, string $txt): \src\notas\domain\entity\PersonaNota {
                $n = $this->createMock(\src\notas\domain\entity\PersonaNota::class);
                $n->method('getIdAsignaturaVo')->willReturn(new \src\asignaturas\domain\value_objects\AsignaturaId($idAsignatura));
                $n->method('getIdNivelVo')->willReturn(new \src\asignaturas\domain\value_objects\NivelId($idAsignatura > 3000 ? 2431 : $idAsignatura));
                $n->method('getNota_txt')->willReturn($txt);

                return $n;
            };
            $filtrosNotas = [];
            $notas = $this->createMock(\src\notas\domain\contracts\PersonaNotaRepositoryInterface::class);
            $notas->method('matching')->willReturnCallback(function (\src\shared\domain\criteria\Criteria $c) use (&$filtrosNotas, $nota): array {
                $f = \Tests\support\DescribeCriteria::filtros($c);
                if (count($f) === 2) {
                    return []; // PlanEstudiosDePersona: sin marca de fin de cuadrienio -> plan 2026
                }
                $filtrosNotas = $f;

                return [$nota(1101, '8/10'), $nota(1102, '7/10'), $nota(9998, 'fin')];
            });
            $this->sustituirServicio(\src\notas\domain\contracts\PersonaNotaRepositoryInterface::class, $notas);
            $otra = $this->createMock(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class);
            $otra->method('matching')->willReturn([$nota(3105, 'Magna cum laude')]);
            $this->sustituirDefinicion(\src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface::class, $otra);

            $json = $this->ejecutar('certificado_emitido_imprimir_mpdf_datos.php', ['id_item' => '7']);

            $this->assertTrue($json['success'], $json['mensaje'] ?? '');
            $d = json_decode($json['data'], true);
            $this->assertSame(['Pla, Joan', 'H 12/26', '24/7/2026', 'checked', 2026, '[{"id":1101}]'], [$d['nom'], $d['certificado'], $d['f_certificado'], $d['chk_firmado'], $d['plan_estudios'], $d['cAsignaturas']]);
            $this->assertStringStartsWith('Matriti, ', $d['lugar_fecha']);
            $this->assertStringContainsString('HISPANIAE', $d['titulo_2']);
            $this->assertSame('', $d['txt_superavit'], 'solo para los de repaso');
            $this->assertSame(['id_schema in [1001,1002]', 'id_nom eq 10011', 'id_nivel between [1100,2500]', 'tipo_acta eq 1'], $filtrosNotas);
            // 1101 aprobada; 1102 inactiva y 9998 (marcador de fin de ciclo) fuera; la opcional 3105 en su hueco 2431.
            $this->assertSame([1101, 2431], array_keys($d['aAprobadas']));
            $this->assertSame(['id_nivel_asig' => 1101, 'id_nivel' => 1101, 'id_asignatura' => 1101, 'nombre_asignatura' => 'Asig 1101', 'creditos' => '9', 'nota_txt' => '8/10'], $d['aAprobadas'][1101]);
            $this->assertSame('Magna cum laude', $d['aAprobadas'][2431]['nota_txt']);
        } finally {
            $_SESSION['oConfig'] = $anterior;
        }
    }

    public function test_cada_ruta_apunta_a_un_fichero_de_controladores_y_al_reves(): void
    {
        $rutas = file_get_contents(self::DIR . '../../../../config/routes.php') ?: '';
        preg_match_all("#'/src/certificados/([a-z_]+)'#", $rutas, $m);
        $declaradas = $m[1];
        sort($declaradas);
        $ficheros = array_map(static fn (string $f): string => basename($f, '.php'), glob(self::DIR . '*.php') ?: []);
        sort($ficheros);

        $this->assertSame($ficheros, $declaradas);
    }
}
