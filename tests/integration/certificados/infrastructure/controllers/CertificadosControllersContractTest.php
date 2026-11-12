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
