<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\certificados\application\CertificadoEmitidoGuardarMessages;
use src\certificados\application\support\CertificadosSession;
use src\menus\application\MenuGuardar;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\entity\MenuDb;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\profesores\application\ProfesorAsignaturaQueData;
use src\shared\config\ConfigGlobal;
use src\tablonanuncios\application\AnuncioDelete;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\application\services\RegionDropdown;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\usuarios\application\usuarioEliminar;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/** Clases pequeñas de varios módulos: anuncios, mensajes de certificados, menús, profesores, cartas y usuarios. */
final class ClasesPequenasVariosModulosTest extends myTest
{
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    // ---- AnuncioDelete ----------------------------------------------------------------------

    public function test_borrar_un_anuncio_vacio_o_inexistente_avisa(): void
    {
        $repo = $this->createMock(AnuncioRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->never())->method('Guardar');
        $uc = new AnuncioDelete($repo);

        $this->assertNotSame('', $uc->execute(''));
        $this->assertNotSame('', $uc->execute(Uuid::uuid4()->toString()));
    }

    public function test_el_borrado_es_logico_marca_la_fecha_de_eliminacion(): void
    {
        $a = new Anuncio();
        $repo = $this->createMock(AnuncioRepositoryInterface::class);
        $repo->method('findById')->willReturn($a);
        $repo->expects($this->once())->method('Guardar')->with($a)->willReturn(true);

        $this->assertSame('', (new AnuncioDelete($repo))->execute(Uuid::uuid4()->toString()));
        $this->assertNotNull($a->getT_eliminado());
    }

    public function test_si_no_se_guarda_el_borrado_avisa(): void
    {
        $repo = $this->createMock(AnuncioRepositoryInterface::class);
        $repo->method('findById')->willReturn(new Anuncio());
        $repo->method('Guardar')->willReturn(false);

        $this->assertNotSame('', (new AnuncioDelete($repo))->execute(Uuid::uuid4()->toString()));
    }

    /**
     * Hallazgo: un id que no es UUID lanza una excepción del VO en lugar de devolver «no se encuentra el anuncio».
     */
    public function test_un_id_que_no_es_uuid_lanza_excepcion(): void
    {
        $this->expectException(\Throwable::class);
        (new AnuncioDelete($this->createMock(AnuncioRepositoryInterface::class)))->execute('no-es-uuid');
    }

    // ---- certificados ----------------------------------------------------------------------

    /**
     * @dataProvider erroresBdProvider
     */
    public function test_los_errores_de_duplicado_se_traducen_y_el_resto_se_devuelve_tal_cual(string $raw, bool $traducido): void
    {
        $r = CertificadoEmitidoGuardarMessages::fromDatabaseError($raw);

        $this->assertSame($traducido, $r !== $raw);
        $this->assertSame($r, CertificadoEmitidoGuardarMessages::fromThrowable(new \RuntimeException($raw)));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function erroresBdProvider(): iterable
    {
        yield 'duplicate key' => ['ERROR: duplicate key value violates', true];
        yield 'restricción por nombre' => ['violates e_certificados_rstgr_ukey', true];
        yield 'unique con id_nom' => ['unique constraint on (id_nom, f_certificado)', true];
        yield 'unique sin id_nom no cuenta' => ['unique constraint on other', false];
        yield 'otro error' => ['connection refused', false];
        yield 'mayúsculas' => ['DUPLICATE KEY', true];
    }

    public function test_el_esquema_de_region_stgr_sale_de_la_sesion_o_es_vacio(): void
    {
        $this->assertSame('H-dlbv', CertificadosSession::esquemaRegionStgr());
        $_SESSION['session_auth'] = ['esquema' => 5];
        $this->assertSame('', CertificadosSession::esquemaRegionStgr());
        $_SESSION['session_auth'] = 'x';
        $this->assertSame('', CertificadosSession::esquemaRegionStgr());
        unset($_SESSION['session_auth']);
        $this->assertSame('', CertificadosSession::esquemaRegionStgr());
    }

    // ---- MenuGuardar ----------------------------------------------------------------------

    public function test_un_menu_nuevo_toma_un_id_nuevo_y_combina_los_bits_de_permiso(): void
    {
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('getNewId')->willReturn(77);
        $guardado = null;
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (MenuDb $m) use (&$guardado): bool {
            $guardado = $m;

            return true;
        });

        $r = (new MenuGuardar($repo))(3, 0, 'on', '10,20', 'Mi menú', 'a=1', 5, ['1', 4]);

        $this->assertSame('', $r);
        $this->assertSame([77, 3, 'Mi menú', 'a=1', 5, 5, true, [10, 20]], [
            $guardado->getId_menu(), $guardado->getId_grupmenu(), $guardado->getMenu(), $guardado->getParametros(),
            $guardado->getId_metamenu(), $guardado->getMenu_perm(), $guardado->isOk(), $guardado->getOrden(),
        ]);
    }

    public function test_un_menu_existente_conserva_sus_permisos_si_no_se_envian_nuevos(): void
    {
        $existente = new MenuDb();
        $existente->setMenu_perm(12);
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->with(9)->willReturn($existente);
        $repo->method('Guardar')->willReturn(true);

        (new MenuGuardar($repo))(3, 9, '', '1', 'x', '', 1, []);

        $this->assertSame(12, $existente->getMenu_perm());
        $this->assertFalse($existente->isOk());
    }

    public function test_menu_inexistente_o_error_de_guardado_avisan(): void
    {
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $this->assertNotSame('', (new MenuGuardar($repo))(3, 9, '', '1', 'x', '', 1, []));

        $repo2 = $this->createMock(MenuDbRepositoryInterface::class);
        $repo2->method('getNewId')->willReturn(1);
        $repo2->method('Guardar')->willReturn(false);
        $repo2->method('getErrorTxt')->willReturn('sql');
        $this->assertStringContainsString('sql', (new MenuGuardar($repo2))(3, 0, '', '1', 'x', '', 1, []));
    }

    /**
     * Hallazgo: un orden vacío se convierte en `[0]` (`explode(',', '')` da una cadena vacía → 0).
     */
    public function test_un_orden_vacio_se_guarda_como_cero(): void
    {
        $m = new MenuDb();
        $repo = $this->createMock(MenuDbRepositoryInterface::class);
        $repo->method('findById')->willReturn($m);
        $repo->method('Guardar')->willReturn(true);

        (new MenuGuardar($repo))(3, 9, '', '', 'x', '', 1, []);

        $this->assertSame([0], $m->getOrden());
    }

    // ---- ProfesorAsignaturaQueData ----------------------------------------------------------------------

    public function test_el_plan_de_las_asignaturas_sale_del_dato_de_la_persona_o_el_plan_actual(): void
    {
        $asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $planes = [];
        $asignaturas->method('getArrayAsignaturasConSeparador')->willReturnCallback(function (bool $op, ?int $plan) use (&$planes): array {
            $planes[] = $plan;

            return [1101 => 'Fil'];
        });
        $notas = $this->createMock(PersonaNotaRepositoryInterface::class);
        $notas->method('matching')->willReturn([]);
        $uc = new ProfesorAsignaturaQueData($asignaturas, new PlanEstudiosDePersona($notas));

        $uc->execute(['plan_estudios' => '1997']);
        $uc->execute([]);
        $uc->execute(['id_nom' => '10011']);

        $this->assertSame([1997, PlanEstudios::PLAN_2026, PlanEstudios::PLAN_2026], $planes);
        $this->assertSame(['aOpciones' => [1101 => 'Fil']], $uc->execute([]));
    }

    // ---- CartaPresentacionUpdate ----------------------------------------------------------------------

    private CartaPresentacionRepositoryInterface&MockObject $cartas;
    private CartaPresentacionDlRepositoryInterface&MockObject $cartasDl;
    private CartaPresentacionExRepositoryInterface&MockObject $cartasEx;
    private CentroRepositoryInterface&MockObject $centros;

    private function cartaUc(): CartaPresentacionUpdate
    {
        $this->cartas = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $this->cartasDl = $this->createMock(CartaPresentacionDlRepositoryInterface::class);
        $this->cartasDl->method('matching')->willReturn([]);
        $this->cartasEx = $this->createMock(CartaPresentacionExRepositoryInterface::class);
        $this->centros = $this->createMock(CentroRepositoryInterface::class);

        return new CartaPresentacionUpdate($this->cartas, $this->cartasDl, $this->cartasEx, $this->centros);
    }

    private function centro(string $dl, string $tipo): Centro
    {
        $c = $this->createMock(Centro::class);
        $c->method('getDl')->willReturn($dl);
        $c->method('getTipo_ctr')->willReturn($tipo);

        return $c;
    }

    public function test_la_carta_exige_centro_y_direccion(): void
    {
        $uc = $this->cartaUc();

        $this->assertFalse($uc->execute(['id_ubi' => 0, 'id_direccion' => 5])['ok']);
        $this->assertFalse($uc->execute(['id_ubi' => 1003])['ok']);
    }

    public function test_una_carta_existente_se_actualiza_en_su_repositorio(): void
    {
        $uc = $this->cartaUc();
        $c = new CartaPresentacion();
        $this->cartas->method('findById')->with(1003, 5)->willReturn($c);
        $this->cartas->expects($this->once())->method('Guardar')->with($c)->willReturn(true);

        $r = $uc->execute(['id_ubi' => 1003, 'id_direccion' => 5, 'pres_nom' => 'Ana', 'pres_telf' => '1', 'pres_mail' => 'a@b.c', 'zona' => 'N', 'observ' => 'x']);

        $this->assertSame(['ok' => true, 'mensaje' => ''], $r);
        $this->assertSame(['Ana', '1', 'a@b.c', 'N', 'x'], [$c->getPres_nom(), $c->getPres_telf(), $c->getPres_mail(), $c->getZona(), $c->getObserv()]);
    }

    /**
     * @dataProvider centrosNuevosProvider
     */
    public function test_una_carta_nueva_va_al_repositorio_de_la_dl_al_de_cr_o_se_rechaza(?string $dl, ?string $tipo, string $destino): void
    {
        $uc = $this->cartaUc();
        $this->cartas->method('findById')->willReturn(null);
        $this->centros->method('findById')->willReturn($dl === null ? null : $this->centro($dl, (string) $tipo));
        $this->cartasDl->expects($destino === 'dl' ? $this->once() : $this->never())->method('Guardar')->willReturn(true);
        $this->cartasEx->expects($destino === 'ex' ? $this->once() : $this->never())->method('Guardar')->willReturn(true);

        $r = $uc->execute(['id_ubi' => 1003, 'id_direccion' => 5]);

        $this->assertSame($destino !== 'ninguno', $r['ok']);
    }

    /**
     * @return iterable<string, array{?string, ?string, string}>
     */
    public static function centrosNuevosProvider(): iterable
    {
        yield 'centro de mi dl' => ['dlb', 'ndl', 'dl'];
        yield 'comisión regional' => ['dlx', 'cr', 'ex'];
        yield 'centro de otra dl' => ['dlx', 'ndl', 'ninguno'];
        yield 'centro inexistente' => [null, null, 'ninguno'];
    }

    public function test_un_fallo_de_guardado_se_devuelve_sin_sanear(): void
    {
        $uc = $this->cartaUc();
        $this->cartas->method('findById')->willReturn(new CartaPresentacion());
        $this->cartas->method('Guardar')->willReturn(false);
        $this->cartasDl->expects($this->never())->method('matching');

        $this->assertFalse($uc->execute(['id_ubi' => 1003, 'id_direccion' => 5])['ok']);
    }

    // ---- CartasPresentacionBuscarOpcionesData ----------------------------------------------------------------------

    /**
     * Hallazgo: las delegaciones ofrecidas están fijadas a la región `H` (`byRegiones(['H'])`).
     */
    public function test_las_delegaciones_del_buscador_estan_fijadas_a_la_region_h(): void
    {
        $dir = $this->createMock(DireccionCentroRepositoryInterface::class);
        $dir->method('getArrayPaises')->willReturn([1 => 'España', 'IT' => 'Italia']);
        $delRepo = $this->createMock(DelegacionRepositoryInterface::class);
        $criterios = [];
        $delRepo->method('matching')->willReturnCallback(function (\src\shared\domain\criteria\Criteria $c) use (&$criterios): array {
            $criterios[] = \Tests\support\DescribeCriteria::filtros($c);

            return [];
        });
        $uc = new CartasPresentacionBuscarOpcionesData($dir, new DelegacionDropdown($delRepo), new RegionDropdown($this->createMock(RegionRepositoryInterface::class)));

        $r = $uc->execute();

        $this->assertSame(['1' => 'España', 'IT' => 'Italia'], $r['opciones_pais']);
        $this->assertStringContainsString('region in [H]', implode('|', $criterios[0]));
        $this->assertSame('frontend/cartaspresentacion/controller/cartas_presentacion_lista.php', $r['paths']['lista']);
        $this->assertSame('que!poblacion!region!pais!dl', $r['hash_lista']['campos_form']);
    }

    // ---- usuarioEliminar ----------------------------------------------------------------------

    public function test_eliminar_usuario_inexistente_avisa_y_existente_lo_elimina(): void
    {
        $repo = $this->createMock(UsuarioRepositoryInterface::class);
        $u = $this->createMock(Usuario::class);
        $repo->method('findById')->willReturnMap([[5, $u], [6, null], [7, $u]]);
        $repo->expects($this->exactly(2))->method('Eliminar')->willReturnOnConsecutiveCalls(true, false);
        $repo->method('getErrorTxt')->willReturn('fk');
        $uc = new usuarioEliminar($repo);

        $this->assertSame(['error' => '', 'data' => 'ok'], $uc->execute(5));
        $this->assertNotSame('', $uc->execute(6)['error']);
        $this->assertStringContainsString('fk', $uc->execute(7)['error']);
    }
}
