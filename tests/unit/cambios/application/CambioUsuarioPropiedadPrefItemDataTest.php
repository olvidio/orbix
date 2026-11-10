<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use src\cambios\application\CambioUsuarioPropiedadPrefItemData;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use Tests\myTest;

/** Datos de la condición configurada para una propiedad vigilada (modal de avisos de cambios). */
final class CambioUsuarioPropiedadPrefItemDataTest extends myTest
{
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1];
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

    private function uc(?CambioUsuarioPropiedadPref $pref = null, ?CasaDlRepositoryInterface $casas = null): CambioUsuarioPropiedadPrefItemData
    {
        $repo = $this->createMock(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $repo->method('findById')->willReturn($pref);

        return new CambioUsuarioPropiedadPrefItemData($repo, $casas ?? $this->createMock(CasaDlRepositoryInterface::class));
    }

    public function test_sin_condicion_guardada_los_dos_valores_van_marcados_por_defecto(): void
    {
        $r = $this->uc()->execute(['objeto' => 'Actividad', 'propiedad' => 'nom_activ']);

        $this->assertSame(['', 'checked', 'checked', 0, [], ''], [$r['operador'], $r['chk_old'], $r['chk_new'], $r['id_item'], $r['aOpcionesCasas'], $r['error']]);
        $this->assertSame(['Actividad', 'nom_activ', ''], [$r['objeto'], $r['propiedad'], $r['valor']]);
    }

    public function test_una_condicion_guardada_con_operador_trae_su_valor_y_las_marcas_reales(): void
    {
        $p = new CambioUsuarioPropiedadPref();
        $p->setOperador('=');
        $p->setValor('ca');
        $p->setValor_old(true);
        $p->setValor_new(false);

        $r = $this->uc($p)->execute(['id_item' => '5', 'objeto' => 'Actividad', 'propiedad' => 'nom_activ']);

        $this->assertSame(['=', 'ca', 'checked', ''], [$r['operador'], $r['valor'], $r['chk_old'], $r['chk_new']]);
        $this->assertSame(5, $r['id_item']);
    }

    public function test_una_condicion_sin_operador_conserva_las_marcas_por_defecto(): void
    {
        $p = new CambioUsuarioPropiedadPref();
        $p->setValor('x');

        $r = $this->uc($p)->execute(['id_item' => '5']);

        $this->assertSame(['x', '', 'checked', 'checked'], [$r['valor'], $r['operador'], $r['chk_old'], $r['chk_new']]);
    }

    public function test_un_id_sin_condicion_en_la_bd_equivale_a_no_tener_ninguna(): void
    {
        $r = $this->uc(null)->execute(['id_item' => '99']);

        $this->assertSame(['', ''], [$r['valor'], $r['operador']]);
    }

    public function test_para_id_ubi_sin_usuario_en_sesion_las_casas_quedan_vacias_sin_consultar(): void
    {
        $casas = $this->createMock(CasaDlRepositoryInterface::class);
        $casas->expects($this->never())->method('getArrayCasas');

        $r = $this->uc(null, $casas)->execute(['propiedad' => 'id_ubi']);

        $this->assertSame([], $r['aOpcionesCasas']);
        $this->assertSame('id_ubi', $r['propiedad']);
    }

    public function test_para_id_ubi_con_usuario_sin_rol_las_casas_quedan_vacias(): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn(null);
        $_SESSION['session_auth']['MiUsuario'] = $u;
        $casas = $this->createMock(CasaDlRepositoryInterface::class);
        $casas->expects($this->never())->method('getArrayCasas');

        $this->assertSame([], $this->uc(null, $casas)->execute(['propiedad' => 'id_ubi'])['aOpcionesCasas']);
    }

    public function test_para_id_ubi_un_usuario_normal_de_sv_pide_solo_las_casas_activas_de_sv(): void
    {
        $u = $this->createMock(Usuario::class);
        $u->method('getId_role')->willReturn(0);
        $_SESSION['session_auth']['MiUsuario'] = $u;
        $casas = $this->createMock(CasaDlRepositoryInterface::class);
        $casas->expects($this->once())->method('getArrayCasas')->with("WHERE active='t' AND sv='t'")->willReturn([1003 => 'Casa A']);

        $r = $this->uc(null, $casas)->execute(['propiedad' => 'id_ubi']);

        $this->assertSame([1003 => 'Casa A'], $r['aOpcionesCasas']);
    }
}
