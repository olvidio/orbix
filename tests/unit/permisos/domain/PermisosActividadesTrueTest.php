<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain;

use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use Tests\myTest;

final class PermisosActividadesTrueTest extends myTest
{
    public function test_es_un_permisos_actividades(): void
    {
        $this->assertInstanceOf(PermisosActividades::class, new PermisosActividadesTrue(5));
    }

    public function test_por_defecto_concede_todo(): void
    {
        $p = new PermisosActividadesTrue(5);

        $this->assertTrue($p->getPermisoActual('datos')->have_perm_activ('crear'));
        $this->assertFalse($p->getPermisoActual('datos')->have_perm_activ('borrar'), 'la actividad propia llega hasta crear (15)');
    }

    public function test_si_la_actividad_no_es_propia_solo_permite_ver(): void
    {
        $p = new PermisosActividadesTrue(5);
        $p->setPropia(false);

        $perm = $p->getPermisoActual('sacd');

        $this->assertTrue($perm->have_perm_activ('ver'));
        $this->assertFalse($perm->have_perm_activ('modificar'));
    }

    public function test_set_propia_acepta_valores_tipo_postgres(): void
    {
        $p = new PermisosActividadesTrue(5);

        $p->setPropia('f');
        $this->assertFalse($p->getPermisoActual('datos')->have_perm_activ('modificar'));

        $p->setPropia('t');
        $this->assertTrue($p->getPermisoActual('datos')->have_perm_activ('modificar'));
    }

    public function test_permiso_on_siempre_es_crear(): void
    {
        $p = new PermisosActividadesTrue(5);
        $p->setPropia(false);

        $this->assertTrue($p->getPermisoOn('datos')->have_perm_activ('crear'));
        $this->assertTrue($p->getPermisoOn(PermisosActividades::AFECTA['sacd'])->have_perm_activ('crear'));
    }

    public function test_el_acceso_al_permiso_actual_no_depende_del_afecta(): void
    {
        $p = new PermisosActividadesTrue(5);

        foreach (array_keys(PermisosActividades::AFECTA) as $afecta) {
            $this->assertTrue($p->getPermisoActual($afecta)->have_perm_activ('crear'), $afecta);
        }
    }

    public function test_permisos_sacd_siempre_concedidos(): void
    {
        $p = new PermisosActividadesTrue(5);

        $this->assertTrue($p->havePermisoSacd(null, false));
        $this->assertTrue($p->havePermisoSacd(7, true));
        $this->assertNull($p->motivoSinPermisoSacd(null, false));
        $this->assertNull($p->motivoSinPermisoSacd(7, true));
    }

    public function test_permiso_crear_sin_procesos(): void
    {
        $p = new PermisosActividadesTrue(5);

        $this->assertSame(['of_responsable_txt' => '', 'status' => 0], $p->getPermisoCrear(true));
        $this->assertSame(['of_responsable_txt' => '', 'status' => 0], $p->getPermisoCrear(false));
    }

    public function test_set_fases_completadas_no_hace_nada(): void
    {
        $p = new PermisosActividadesTrue(5);

        $p->setFasesCompletadas([1, 2, 3]);

        $this->assertTrue($p->getPermisoActual('datos')->have_perm_activ('crear'));
    }

    public function test_set_actividad_solo_con_id_no_consulta_nada(): void
    {
        $p = new PermisosActividadesTrue(5);

        $p->setActividad(12345);

        $this->assertTrue($p->getPermisoActual('datos')->have_perm_activ('crear'));
    }

    public function test_set_actividad_con_tipo_y_dl_ajusta_si_es_propia(): void
    {
        $p = new PermisosActividadesTrue(5);

        $p->setActividad(12345, '171001', 'dl_ajena_inexistente');
        $this->assertFalse($p->getPermisoActual('datos')->have_perm_activ('modificar'));

        $p->setActividad(12345, '171001', \src\shared\config\ConfigGlobal::mi_delef());
        $this->assertTrue($p->getPermisoActual('datos')->have_perm_activ('crear'));
    }

    public function test_set_actividad_con_tipo_pero_sin_dl_se_ignora(): void
    {
        $p = new PermisosActividadesTrue(5);
        $p->setPropia(false);

        $p->setActividad(12345, '171001', '');
        $p->setActividad(12345, '', 'dlb');

        $this->assertFalse($p->getPermisoActual('datos')->have_perm_activ('modificar'));
    }

    public function test_serializa_solo_el_id_de_usuario(): void
    {
        $this->assertSame(['idUsuario' => 5], (new PermisosActividadesTrue(5))->__serialize());
    }

    public function test_ida_y_vuelta_de_serializacion(): void
    {
        $copia = unserialize(serialize(new PermisosActividadesTrue(42)));

        $this->assertInstanceOf(PermisosActividadesTrue::class, $copia);
        $this->assertSame(['idUsuario' => 42], $copia->__serialize());
    }

    /**
     * @dataProvider datosDeSesionProvider
     * @param array<string, mixed> $data
     */
    public function test_unserialize_tolera_el_formato_antiguo_y_valores_raros(array $data, int $esperado): void
    {
        $p = new PermisosActividadesTrue(1);

        $p->__unserialize($data);

        $this->assertSame(['idUsuario' => $esperado], $p->__serialize());
    }

    /**
     * @return iterable<string, array{array<string, mixed>, int}>
     */
    public static function datosDeSesionProvider(): iterable
    {
        yield 'entero' => [['idUsuario' => 7], 7];
        yield 'string numérico' => [['idUsuario' => '8'], 8];
        yield 'formato del padre (con más claves)' => [['idUsuario' => 9, 'aPermDl' => [], 'bpropia' => true], 9];
        yield 'sin clave' => [[], 0];
        yield 'no numérico' => [['idUsuario' => 'abc'], 0];
        yield 'null' => [['idUsuario' => null], 0];
    }
}
