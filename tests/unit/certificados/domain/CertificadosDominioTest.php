<?php

declare(strict_types=1);

namespace Tests\unit\certificados\domain;

use PHPUnit\Framework\MockObject\MockObject;
use src\certificados\application\CertificadoEmitidoGuardarMessages;
use src\certificados\domain\CertificadoEmitidoEnviar;
use src\certificados\domain\CertificadoEmitidoSelect;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\certificados\domain\entity\CertificadoRecibido;
use src\certificados\domain\Select_certificados_de_una_persona;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\Trasladar;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Lógica de `certificados` que vive en `domain/` y no se veía en la fase 1 (el chequeo solo miraba `application/`):
 * enviar un certificado emitido a la dl del alumno, el listado de emitidos, el widget de recibidos de una persona y
 * los mensajes de error al guardar.
 */
final class CertificadosDominioTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private CertificadoEmitidoRepositoryInterface&MockObject $emitidos;
    private DelegacionRepositoryInterface&MockObject $delegaciones;
    private PersonaFinderService&MockObject $finder;
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'];
        $this->emitidos = $this->createMock(CertificadoEmitidoRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->sustituirServicio(PersonaFinderService::class, $this->finder);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_SESSION['session_auth'] = $this->sesionAnterior;
        parent::tearDown();
    }

    private function emitido(int $idItem = 7, ?int $idNom = 10011, string $certificado = 'H 12/26'): CertificadoEmitido
    {
        $c = new CertificadoEmitido();
        $c->setId_item($idItem);
        $c->setId_nom($idNom);
        $c->setCertificado($certificado);
        $c->setF_certificado(new DateTimeLocal('2026-07-24'));

        return $c;
    }

    // ------------------------------------------------------------------ enviar

    private function enviar(?Trasladar $trasladar = null, ?AnuncioRepositoryInterface $anuncios = null): CertificadoEmitidoEnviar
    {
        return new CertificadoEmitidoEnviar(
            $this->emitidos,
            $this->delegaciones,
            $anuncios ?? $this->createMock(AnuncioRepositoryInterface::class),
            $trasladar ?? $this->createMock(Trasladar::class),
        );
    }

    /** @param list<string> $esquemas */
    private function personaEn(string $dl, array $esquemas = ['Galbel-crGalbelv']): void
    {
        $p = $this->createMock(PersonaPub::class);
        $p->method('getApellidosNombre')->willReturn('Pla, Joan');
        $p->method('getDlVo')->willReturn(new \src\ubis\domain\value_objects\DelegacionCode($dl));
        $this->finder->method('buscarEnTodasRegiones')->willReturn(array_map(static fn (string $e): array => ['esquema' => $e, 'persona' => $p], $esquemas));
    }

    public function test_enviar_un_certificado_que_no_existe(): void
    {
        $this->emitidos->method('findById')->willReturn(null);

        $this->assertNotSame('', $this->enviar()->execute(7));
    }

    public function test_una_persona_de_paso_no_se_puede_enviar(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido(7, -1005));
        $this->finder->expects($this->never())->method('buscarEnTodasRegiones');

        $this->assertStringContainsString('imprimir', $this->enviar()->execute(7));
    }

    public function test_si_no_se_encuentra_a_la_persona_o_hay_varias_no_se_envia(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido());
        $this->finder->method('buscarEnTodasRegiones')->willReturn([]);
        $this->assertStringContainsString('10011', $this->enviar()->execute(7));
    }

    public function test_con_la_persona_dada_de_alta_en_varias_dl_se_listan_y_no_se_envia(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido());
        $this->personaEn('crGalbel', ['Galbel-crGalbelv', 'H-dlbv']);
        $trasladar = $this->createMock(Trasladar::class);
        $trasladar->expects($this->never())->method('copiar_certificados_a_dl');

        $r = $this->enviar($trasladar)->execute(7);

        $this->assertStringContainsString('Galbel-crGalbelv', $r);
        $this->assertStringContainsString('H-dlbv', $r);
    }

    public function test_una_dl_que_no_esta_en_orbix_hay_que_enviarla_a_mano(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido());
        $this->personaEn('dlzz');
        $this->delegaciones->method('mi_region_stgr')->willReturn(['esquema_region_stgr' => 'Z-Z', 'esquema_dl' => 'Z-dlzz']);
        $trasladar = $this->createMock(Trasladar::class);
        $trasladar->expects($this->never())->method('copiar_certificados_a_dl');

        $this->assertStringContainsString('manualmente', $this->enviar($trasladar)->execute(7));
    }

    public function test_un_error_al_buscar_la_region_de_la_dl_destino_se_devuelve(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido());
        $this->personaEn('crGalbel');
        $this->delegaciones->method('mi_region_stgr')->willThrowException(new \RuntimeException('dl sin región'));

        $this->assertStringContainsString('dl sin región', $this->enviar()->execute(7));
    }

    public function test_envia_copiando_a_la_dl_del_alumno_y_avisa_en_el_tablon_de_su_region(): void
    {
        $c = $this->emitido();
        $this->emitidos->method('findById')->with(7)->willReturn($c);
        $this->personaEn('crGalbel');
        $this->delegaciones->method('mi_region_stgr')->with('crGalbel')->willReturn(['esquema_region_stgr' => 'Galbel-Galbelv', 'esquema_dl' => 'Galbel-crGalbelv']);
        $trasladar = $this->createMock(Trasladar::class);
        $trasladar->expects($this->once())->method('setReg_dl_dst')->with('Galbel-crGalbelv');
        $trasladar->expects($this->once())->method('copiar_certificados_a_dl')->with($c)->willReturn(true);
        $trasladar->method('getError')->willReturn('');
        $anuncio = null;
        $anuncios = $this->createMock(AnuncioRepositoryInterface::class);
        $anuncios->expects($this->once())->method('Guardar')->willReturnCallback(function (Anuncio $a) use (&$anuncio): bool {
            $anuncio = $a;

            return true;
        });

        $this->assertSame('', $this->enviar($trasladar, $anuncios)->execute(7));

        $this->assertInstanceOf(Anuncio::class, $anuncio);
        $this->assertSame('Galbel-Galbelv', $anuncio->getEsquemaDestinoVo()?->value());
        $this->assertStringContainsString('H 12/26', $anuncio->getTextoAnuncioVo()?->value() ?? '');
        $this->assertStringContainsString('Pla, Joan', $anuncio->getTextoAnuncioVo()?->value() ?? '');
        $this->assertNotNull($c->getF_enviado());
    }

    /**
     * Hallazgo: el anuncio «se ha recibido el certificado» se publica aunque la copia a la dl del alumno haya
     * fallado (el error se devuelve, pero el aviso ya está en el tablón de su región).
     */
    public function test_el_anuncio_se_publica_aunque_falle_la_copia(): void
    {
        $this->emitidos->method('findById')->willReturn($this->emitido());
        $this->personaEn('crGalbel');
        $this->delegaciones->method('mi_region_stgr')->willReturn(['esquema_region_stgr' => 'Galbel-Galbelv', 'esquema_dl' => 'Galbel-crGalbelv']);
        $trasladar = $this->createMock(Trasladar::class);
        $trasladar->method('copiar_certificados_a_dl')->willReturn(false);
        $trasladar->method('getError')->willReturn('no se pudo copiar');
        $anuncios = $this->createMock(AnuncioRepositoryInterface::class);
        $anuncios->expects($this->once())->method('Guardar');

        $this->assertSame('no se pudo copiar', $this->enviar($trasladar, $anuncios)->execute(7));
    }

    // ------------------------------------------------------------------ listado de emitidos

    /** @var list<string> */
    private array $filtrosListado = [];

    /** @param list<array{certificado: CertificadoEmitido, tiene_documento: bool}> $filas */
    private function listado(array $filas = [], bool $soyRegion = true): CertificadoEmitidoSelect
    {
        $this->emitidos->method('matchingSinDocumento')->willReturnCallback(function (Criteria $c) use ($filas): array {
            $this->filtrosListado = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return $filas;
        });
        $this->delegaciones->method('soy_region_stgr')->willReturn($soyRegion);
        $locales = $this->createMock(LocalRepositoryInterface::class);
        $locales->method('findById')->willReturnCallback(function (string $id): ?Local {
            if ($id !== 'ca_ES.UTF-8') {
                return null;
            }
            $l = $this->createMock(Local::class);
            $l->method('getNomIdiomaAsString')->willReturn('català');

            return $l;
        });

        return new CertificadoEmitidoSelect($this->emitidos, $this->delegaciones, $locales);
    }

    public function test_sin_numero_se_listan_los_no_enviados_del_curso_de_mi_region(): void
    {
        $this->listado()->getCamposVista('', '2025-10-01', '2026-09-30');

        $mia = \src\shared\config\ConfigGlobal::mi_region_dl();
        $this->assertSame([
            'f_certificado between [2025-10-01,2026-09-30]',
            "esquema_emisor eq $mia",
            'f_enviado is_null',
            'f_certificado desc',
            'certificado desc',
        ], $this->filtrosListado);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function numerosProvider(): iterable
    {
        $anyo = date('y');
        yield 'solo el número: de este año' => ['12', "12/$anyo"];
        yield 'número y año' => ['12/25', '12/25'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('numerosProvider')]
    public function test_con_un_numero_se_busca_el_certificado_de_mi_region(string $buscado, string $sufijo): void
    {
        $this->listado()->getCamposVista($buscado, '', '');

        $region = \src\shared\config\ConfigGlobal::mi_region();
        $this->assertSame("certificado matches $region $sufijo", $this->filtrosListado[0]);
    }

    public function test_un_texto_que_no_empieza_por_numero_se_busca_tal_cual(): void
    {
        $this->listado()->getCamposVista('H 12', '', '');

        $this->assertSame('certificado matches H 12', $this->filtrosListado[0]);
    }

    public function test_cada_fila_del_listado_lleva_sus_datos_y_el_token_de_borrar(): void
    {
        $c = $this->emitido(7, 10011);
        $c->setFirmado(true);
        $c->setIdiomaVo('ca_ES.UTF-8');
        $c->setDestino('crGalbel');
        $c->setF_enviado(new DateTimeLocal('2026-07-30'));
        $sinNombre = $this->emitido(8, 10012, 'H 13/26');
        $sinNombre->setNom('Nombre a mano');
        $p = $this->createMock(PersonaDl::class);
        $p->method('getNombreApellidos')->willReturn('Joan Pla');
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(static fn (int $id): ?PersonaDl => $id === 10011 ? $p : null);

        $r = $this->listado([['certificado' => $c, 'tiene_documento' => true], ['certificado' => $sinNombre, 'tiene_documento' => false]])
            ->getCamposVista('', '2025-10-01', '2026-09-30');

        $this->assertTrue($r['success']);
        $fila = $r['a_valores'][1];
        $this->assertSame([7, 'H 12/26', '24/7/2026', 'Joan Pla', 'Sí', 'Sí', 'català', 'crGalbel', '30/7/2026'], [$fila['sel'], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8]]);
        $this->assertSame(['id_item' => 7], HashB::open($fila['ctx_eliminar'], 'certificado_emitido_delete'));
        $this->assertSame(['Nombre a mano', 'No', ''], [$r['a_valores'][2][3], $r['a_valores'][2][4], $r['a_valores'][2][5]]);
    }

    public function test_solo_la_region_del_stgr_puede_eliminar_modificar_subir_y_enviar(): void
    {
        $this->assertCount(5, $this->listado([], true)->getCamposVista('', '', '')['a_botones']);
    }

    public function test_fuera_de_la_region_del_stgr_solo_se_puede_descargar(): void
    {
        $this->assertSame(['descargar pdf'], array_column($this->listado([], false)->getCamposVista('', '', '')['a_botones'], 'txt'));
    }

    // ------------------------------------------------------------------ widget de recibidos de una persona

    public function test_el_widget_lista_los_certificados_recibidos_de_la_persona(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn($this->createMock(PersonaDl::class));
        $r1 = new CertificadoRecibido();
        $r1->setId_item(31);
        $r1->setCertificado('crGalbel 4/26');
        $r1->setFirmado(false);
        $r1->setF_certificado(new DateTimeLocal('2026-03-01'));
        $r1->setF_recibido(new DateTimeLocal('2026-03-10'));
        $r1->setDocumento('%PDF');
        $filtros = [];
        $repo = $this->createMock(CertificadoRecibidoRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use (&$filtros, $r1): array {
            $filtros = array_merge(DescribeCriteria::filtros($c), DescribeCriteria::orden($c));

            return [$r1];
        });
        $w = new Select_certificados_de_una_persona($repo);
        $w->setId_pau(10011);
        $w->setPau('p');
        $w->setQid_sel('31');

        $d = $w->getSegmentData();

        $this->assertSame(['id_nom eq 10011', 'f_certificado asc'], $filtros);
        $this->assertSame('select_certificados_de_una_persona', $d['segment_tipo']);
        $fila = $d['tabla']['valores'][1];
        $this->assertSame([31, 'crGalbel 4/26', '1/3/2026', 'No', 'Sí', '10/3/2026'], [$fila['sel'], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5]]);
        $this->assertSame(['id_item' => 31], HashB::open($fila['ctx_eliminar'], 'certificado_recibido_delete'));
        $this->assertSame('31', $d['tabla']['valores']['select']);
        $this->assertSame(['nuevo' => 1, 'id_nom' => 10011], $d['url_nuevo_spec']['query']);
        $this->assertCount(3, $d['tabla']['botones']);
    }

    /** Hallazgo: con una persona que no se encuentra, el widget lanza una excepción con la ruta del fichero. */
    public function test_el_widget_con_una_persona_que_no_existe_lanza_excepcion(): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn(null);
        $w = new Select_certificados_de_una_persona($this->createMock(CertificadoRecibidoRepositoryInterface::class));
        $w->setId_pau(10019);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Select_certificados_de_una_persona\.php/');

        $w->getSegmentData();
    }

    // ------------------------------------------------------------------ mensajes al guardar

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function erroresProvider(): iterable
    {
        yield 'clave duplicada' => ['SQLSTATE[23505]: duplicate key value violates unique constraint "x"', true];
        yield 'restricción de persona y fecha' => ['violates e_certificados_rstgr_ukey', true];
        yield 'unique de id_nom' => ['unique constraint on (id_nom, f_certificado)', true];
        yield 'unique sin id_nom' => ['unique constraint on (certificado)', false];
        yield 'otro error' => ['connection refused', false];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('erroresProvider')]
    public function test_los_duplicados_se_traducen_y_el_resto_se_devuelve_tal_cual(string $raw, bool $traducido): void
    {
        $msg = CertificadoEmitidoGuardarMessages::fromDatabaseError($raw);

        $this->assertSame($traducido, $msg !== $raw);
        $this->assertSame(CertificadoEmitidoGuardarMessages::fromDatabaseError($raw), CertificadoEmitidoGuardarMessages::fromThrowable(new \RuntimeException($raw)));
    }

    /**
     * Hallazgo: cualquier «duplicate key», de cualquier restricción (p. ej. el número de certificado), se presenta
     * como «ya existe un certificado para esta persona con la misma fecha».
     */
    public function test_cualquier_clave_duplicada_se_presenta_como_misma_persona_y_fecha(): void
    {
        $msg = CertificadoEmitidoGuardarMessages::fromDatabaseError('duplicate key value violates unique constraint "e_certificados_certificado_key"');

        $this->assertStringContainsString('misma fecha', $msg);
    }
}
