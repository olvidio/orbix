<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\cambios\application\CambioUsuarioObjetoPrefGuardar;
use src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\shared\security\HashB;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/** Preferencias de aviso por objeto: guardado de la preferencia y listado de campos configurables. */
final class CambioUsuarioObjetoPrefTest extends myTest
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

    // ---- CambioUsuarioObjetoPrefGuardar ----------------------------------------------------------------------

    private CambioUsuarioObjetoPrefRepositoryInterface&MockObject $repo;

    private function guardar(): CambioUsuarioObjetoPrefGuardar
    {
        $this->repo = $this->createMock(CambioUsuarioObjetoPrefRepositoryInterface::class);

        return new CambioUsuarioObjetoPrefGuardar($this->repo);
    }

    public function test_sin_usuario_no_se_guarda_nada(): void
    {
        $uc = $this->guardar();
        $this->repo->expects($this->never())->method('Guardar');

        $this->assertSame(0, $uc->execute(['objeto' => 'Actividad'])['id_item_usuario_objeto']);
        $this->assertNotSame('', $uc->execute(['id_usuario' => '0'])['error']);
    }

    /**
     * Hallazgo: `aviso_tipo` ausente (0) no es un tipo válido y el value object lanza `InvalidArgumentException`
     * en lugar de devolver `['error' => …]`.
     */
    public function test_sin_tipo_de_aviso_se_lanza_una_excepcion(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);

        $this->expectException(\InvalidArgumentException::class);
        $uc->execute(['id_usuario' => '55', 'id_tipo_activ' => '112001', 'objeto' => 'Actividad']);
    }

    public function test_una_preferencia_nueva_toma_id_nuevo_y_guarda_todos_los_campos(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(500);
        $guardada = null;
        $this->repo->expects($this->once())->method('Guardar')->willReturnCallback(function (CambioUsuarioObjetoPref $p) use (&$guardada): bool {
            $guardada = $p;

            return true;
        });

        $r = $uc->execute([
            'id_usuario' => '55', 'id_tipo_activ' => '112001', 'dl_propia' => 'on', 'objeto' => 'Actividad',
            'aviso_tipo' => '2', 'id_fase_ref' => '30', 'aviso_off' => 'on', 'aviso_on' => '', 'aviso_outdate' => 't',
        ]);

        $this->assertSame(['error' => '', 'id_item_usuario_objeto' => 500], $r);
        $this->assertSame([55, 'dlb', '112001', 'Actividad', 2, 30], [
            $guardada->getId_usuario(), $guardada->getDl_org(), $guardada->getId_tipo_activ_txt(), $guardada->getObjeto(),
            $guardada->getAviso_tipo(), $guardada->getId_fase_ref(),
        ]);
        $this->assertTrue($guardada->isAviso_off());
        $this->assertFalse($guardada->isAviso_on());
        $this->assertTrue($guardada->isAviso_outdate());
    }

    public function test_si_no_es_de_la_dl_propia_el_organizador_es_x(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);
        $g = null;
        $this->repo->method('Guardar')->willReturnCallback(function (CambioUsuarioObjetoPref $p) use (&$g): bool {
            $g = $p;

            return true;
        });

        $uc->execute(['id_usuario' => '55', 'id_tipo_activ' => '112001', 'objeto' => 'Actividad', 'aviso_tipo' => '1']);

        $this->assertSame('x', $g->getDl_org());
    }

    /**
     * @dataProvider tiposCortosProvider
     */
    public function test_un_tipo_de_actividad_corto_se_completa_con_puntos_y_uno_largo_se_rechaza(string $tipo, string $esperado, bool $valido): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);
        $g = null;
        $this->repo->method('Guardar')->willReturnCallback(function (CambioUsuarioObjetoPref $p) use (&$g): bool {
            $g = $p;

            return true;
        });

        $r = $uc->execute(['id_usuario' => '55', 'id_tipo_activ' => $tipo, 'objeto' => 'Actividad', 'aviso_tipo' => '1']);

        if ($valido) {
            $this->assertSame('', $r['error']);
            $this->assertSame($esperado, $g->getId_tipo_activ_txt());
        } else {
            $this->assertNotSame('', $r['error']);
            $this->assertNull($g);
        }
    }

    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function tiposCortosProvider(): iterable
    {
        yield 'seis dígitos' => ['112001', '112001', true];
        yield 'tres dígitos' => ['112', '112...', true];
        yield 'uno' => ['1', '1.....', true];
        yield 'vacío (el relleno máximo es de 5 puntos)' => ['', '', false];
        yield 'siete dígitos' => ['1120012', '', false];
    }

    public function test_una_preferencia_existente_se_reutiliza_y_los_centros_vacios_o_cero_se_descartan(): void
    {
        $uc = $this->guardar();
        $existente = new CambioUsuarioObjetoPref();
        $existente->setId_item_usuario_objeto(77);
        $this->repo->method('findById')->with(77)->willReturn($existente);
        $this->repo->expects($this->never())->method('getNewId');
        $this->repo->method('Guardar')->willReturn(true);

        $r = $uc->execute(['id_usuario' => '55', 'id_item_usuario_objeto' => '77', 'id_tipo_activ' => '112001', 'objeto' => 'Actividad', 'aviso_tipo' => '1', 'casas' => ['1003', '', '0', 1004, [9]]]);

        $this->assertSame(77, $r['id_item_usuario_objeto']);
        $this->assertSame('1003,1004', $existente->getCsv_id_pau());
    }

    public function test_un_fallo_de_guardado_devuelve_error_y_cero(): void
    {
        $uc = $this->guardar();
        $this->repo->method('getNewId')->willReturn(1);
        $this->repo->method('Guardar')->willReturn(false);

        $r = $uc->execute(['id_usuario' => '55', 'aviso_tipo' => '1']);

        $this->assertNotSame('', $r['error']);
        $this->assertSame(0, $r['id_item_usuario_objeto']);
    }

    // ---- CambioUsuarioObjetoPrefPropiedadesData ----------------------------------------------------------------------

    private function propiedades(?array $guardadas = null): CambioUsuarioObjetoPrefPropiedadesData
    {
        $repo = $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $repo->method('matching')->willReturn($guardadas ?? []);

        return new CambioUsuarioObjetoPrefPropiedadesData($repo);
    }

    private function conUsuario(?int $idRole): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn($idRole);
        $_SESSION['session_auth']['MiUsuario'] = $u;
    }

    public function test_sin_objeto_no_se_devuelve_nada(): void
    {
        $r = $this->propiedades()->execute(['id_item_usuario_objeto' => '5']);

        $this->assertSame(['error' => '', 'objeto' => '', 'id_item_usuario_objeto' => 5, 'propiedades' => []], $r);
    }

    public function test_sin_usuario_o_sin_rol_o_con_objeto_desconocido_hay_error(): void
    {
        $uc = $this->propiedades();
        $sinUsuario = $uc->execute(['objeto' => 'Actividad']);
        $this->conUsuario(null);
        $sinRol = $uc->execute(['objeto' => 'Actividad']);
        $this->conUsuario(0);
        $desconocido = $uc->execute(['objeto' => 'Inventado']);

        $this->assertNotSame('', $sinUsuario['error']);
        $this->assertNotSame('', $sinRol['error']);
        $this->assertStringContainsString('Inventado', $desconocido['error']);
        $this->assertSame([], $desconocido['propiedades']);
    }

    public function test_los_campos_con_aviso_se_listan_sin_id_schema_y_sin_marcar_si_no_hay_preferencia(): void
    {
        $this->conUsuario(0);

        $r = $this->propiedades()->execute(['objeto' => 'Actividad']);

        $this->assertSame('', $r['error']);
        $this->assertNotSame([], $r['propiedades']);
        foreach ($r['propiedades'] as $p) {
            $this->assertNotSame('id_schema', $p['nom_prop']);
            $this->assertSame('', $p['chk_prop']);
            $this->assertSame(_("cualquier cambio"), $p['condicion']);
        }
        $this->assertArrayNotHasKey('ctx_guardar_propiedades', $r);
    }

    public function test_las_propiedades_ya_guardadas_salen_marcadas_con_su_id_y_su_payload(): void
    {
        $this->conUsuario(0);
        $primera = $this->propiedades()->execute(['objeto' => 'Actividad'])['propiedades'][0]['nom_prop'];
        $p = new CambioUsuarioPropiedadPref();
        $p->setId_item(42);
        $p->setPropiedad($primera);
        $p->setOperador('=');
        $p->setValor('ca');

        $r = $this->propiedades([$p])->execute(['objeto' => 'Actividad', 'id_item_usuario_objeto' => '5']);

        $marcada = $r['propiedades'][0];
        $this->assertSame(['checked', '42'], [$marcada['chk_prop'], $marcada['id_item']]);
        $this->assertSame(['iid_item' => 42, 'spropiedad' => $primera, 'soperador' => '=', 'svalor' => 'ca', 'bvalor_old' => 'f', 'bvalor_new' => 'f'], json_decode($marcada['cambio_prop'], true));
        $this->assertSame(['id_item_usuario_objeto' => 5], HashB::open($r['ctx_guardar_propiedades'], 'cambio_usuario_propiedad_pref_guardar_todas'));
    }
}
