<?php

declare(strict_types=1);

namespace Tests\integration\devel_db_admin\application;

use src\devel_db_admin\application\ApptablesVerificarGlobal;
use src\devel_db_admin\application\DbPropiedadesFormData;
use Tests\myTest;

/** Pantallas de administración de BD sobre la configuración real: solo lectura (listados y comprobaciones). */
final class DevelDbAdminLecturaIntegracionTest extends myTest
{
    private DbPropiedadesFormData $formData;

    public function setUp(): void
    {
        parent::setUp();
        $this->formData = $GLOBALS['container']->get(DbPropiedadesFormData::class);
    }

    public function test_cada_operacion_firma_las_acciones_que_ofrece(): void
    {
        $this->assertArrayHasKey('ctx_apptables', $this->formData->build(['op' => 'apptables_esquemas', 'default_esquema' => '']));

        $ref = $this->formData->build(['op' => 'db_que_esquema_ref']);
        foreach (['ctx_crear', 'ctx_copiar', 'ctx_eliminar', 'ctx_crear_usuarios'] as $k) {
            $this->assertNotSame('', $ref[$k], $k);
        }
        $this->assertIsArray($ref['a_opciones_regiones']);

        $ren = $this->formData->build(['op' => 'db_cambiar_nombre_esquemas']);
        foreach (['ctx_renombrar', 'ctx_verificar', 'ctx_corregir'] as $k) {
            $this->assertNotSame('', $ren[$k], $k);
        }
        $this->assertIsArray($ren['a_esquemas_union']);

        $this->assertNotSame('', $this->formData->build(['op' => 'db_mover_tablas'])['ctx_mover']);
    }

    public function test_absorber_ofrece_esquemas_con_clave_no_vacia(): void
    {
        $r = $this->formData->build(['op' => 'db_absorber_esquema_que']);

        $this->assertNotSame('', $r['ctx_absorber']);
        foreach ($r['a_posibles_esquemas'] as $clave => $etiqueta) {
            $this->assertNotSame('', (string) $clave);
            $this->assertIsString($etiqueta);
        }
    }

    public function test_los_esquemas_con_tabla_son_una_lista_sin_vacios(): void
    {
        $r = $this->formData->build(['op' => 'db_mover_esquemas_con_tabla', 'tabla' => 'tabla_que_no_existe_tst']);

        $this->assertSame([], array_filter($r['a_esquemas_con_tabla'], static fn (string $e): bool => $e === ''));
        $this->assertIsList($r['a_esquemas_con_tabla']);
    }

    public function test_una_app_sin_tablas_globales_declaradas_no_se_verifica(): void
    {
        $this->assertSame([], (new ApptablesVerificarGlobal())->verificar('app_que_no_existe_tst'));
    }

    public function test_las_tablas_globales_de_ubiscamas_existen_en_comun(): void
    {
        $diag = (new ApptablesVerificarGlobal())->verificar('ubiscamas');

        $this->assertSame('ubiscamas', $diag['app']);
        $this->assertSame(['public.du_habitaciones', 'public.du_camas'], $diag['principal']);
    }

    public function test_la_comprobacion_de_tabla_distingue_existentes_de_inexistentes(): void
    {
        $verificador = new ApptablesVerificarGlobal();
        $pdo = (new \src\shared\infrastructure\persistence\DBConnection((new \src\shared\infrastructure\persistence\ConfigDB('importar'))->getEsquema('public')))->getPDO();
        $pdo->exec('SET search_path TO public');
        $existe = new \ReflectionMethod($verificador, 'existeTabla');
        $existe->setAccessible(true);

        $this->assertTrue($existe->invoke($verificador, $pdo, 'public.du_camas'));
        $this->assertFalse($existe->invoke($verificador, $pdo, 'public.tabla_que_no_existe_tst'));

        $assert = new \ReflectionMethod($verificador, 'assertTablas');
        $assert->setAccessible(true);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('public.tabla_que_no_existe_tst');
        $assert->invoke($verificador, $pdo, ['public.du_camas', 'public.tabla_que_no_existe_tst'], 'comun');
    }
}
