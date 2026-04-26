<?php

namespace Tests\integration\actividadessacd\application;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividadessacd\application\ComunicacionActividadesSacdData;
use src\actividadessacd\application\ComunicacionActividadesSacdEnviar;
use src\actividadessacd\application\ListaActividadesSacdData;
use src\actividadessacd\application\SacdAsignarAuto;
use src\actividadessacd\application\SacdsDisponiblesData;
use src\actividadessacd\application\SacdsEncargadosData;
use src\actividadessacd\application\SolapesSacdData;
use src\actividadessacd\application\services\ActividadesSacdHelper;
use src\actividadessacd\application\services\ComunicarActividadesSacdService;
use Tests\myTest;

/**
 * Smoke / contrato de salida para los casos de uso pesados del modulo
 * `actividadessacd` que estan fuertemente acoplados a `$GLOBALS`, sesion,
 * {@see ConfigGlobal}, repos y `frontend\shared\web\Periodo`. Complementa los unitarios
 * con mocks (SacdAsignar, SacdEliminar, SacdReordenar, TextoComunicacion*)
 * cubriendo que las llamadas no rompen y la estructura es la esperada.
 *
 * Para evitar crear datos de negocio, los tests usan periodos futuros o
 * ids imposibles cuando buscan listas para garantizar que devuelven
 * vacio sin tocar filas reales.
 */
class ActividadesSacdHeavyUseCasesIntegrationTest extends myTest
{
    public function test_sacds_encargados_sin_id_activ_devuelve_vacio(): void
    {
        $out = SacdsEncargadosData::execute([
            'id_activ' => 0,
            'id_tipo_activ' => '',
            'dl_org' => '',
        ]);

        $this->assertArrayHasKey('id_activ', $out);
        $this->assertArrayHasKey('permite_ver', $out);
        $this->assertArrayHasKey('permite_modificar', $out);
        $this->assertArrayHasKey('sacds', $out);
        $this->assertSame(0, $out['id_activ']);
        $this->assertFalse($out['permite_ver']);
        $this->assertFalse($out['permite_modificar']);
        $this->assertSame([], $out['sacds']);
    }

    public function test_sacds_encargados_actividad_inexistente_devuelve_sacds_vacio(): void
    {
        $out = SacdsEncargadosData::execute([
            'id_activ' => 999999999,
            'id_tipo_activ' => '271000',
            'dl_org' => ConfigGlobal::mi_delef(),
        ]);

        $this->assertArrayHasKey('id_activ', $out);
        $this->assertArrayHasKey('sacds', $out);
        $this->assertSame(999999999, $out['id_activ']);
        $this->assertIsArray($out['sacds']);
        $this->assertCount(0, $out['sacds']);
    }

    public function test_sacds_disponibles_sin_encargos_app_devuelve_estructura(): void
    {
        $out = SacdsDisponiblesData::execute([
            'id_activ' => 999999999,
            'seleccion' => 0,
        ]);

        $this->assertArrayHasKey('id_activ', $out);
        $this->assertArrayHasKey('sacds_ctr', $out);
        $this->assertArrayHasKey('sacds_todos', $out);
        $this->assertIsArray($out['sacds_ctr']);
        $this->assertIsArray($out['sacds_todos']);
    }

    /**
     * @dataProvider providerProcesosInstalado
     */
    public function test_lista_actividades_sacd_tipo_invalido_devuelve_estructura(bool $procesosInstalado): void
    {
        $bak = $this->backupSessionAppsConfig();
        $permBak = $_SESSION['oPermActividades'];
        $this->applySessionAppInstalled('procesos', $procesosInstalado, 979701);
        $this->refreshPermActividadesSession();

        try {
            $out = ListaActividadesSacdData::execute([
                'tipo' => 'tipo_inexistente',
                'year' => '2099',
                'periodo' => 'tot_any',
                'empiezamin' => '',
                'empiezamax' => '',
            ]);

            $this->assertArrayHasKey('titulo', $out);
            $this->assertArrayHasKey('tipo', $out);
            $this->assertArrayHasKey('inicio_iso', $out);
            $this->assertArrayHasKey('fin_iso', $out);
            $this->assertArrayHasKey('texto_fase_ok_sacd', $out);
            $this->assertArrayHasKey('mostrar_nota_falta_sacd', $out);
            $this->assertArrayHasKey('perm_des', $out);
            $this->assertArrayHasKey('filas', $out);
            $this->assertSame('tipo_inexistente', $out['tipo']);
            $this->assertFalse($out['mostrar_nota_falta_sacd']);
            $this->assertIsArray($out['filas']);
            $this->assertIsString($out['inicio_iso']);
            $this->assertIsString($out['fin_iso']);
        } finally {
            $_SESSION['oPermActividades'] = $permBak;
            $this->restoreSessionAppsConfig($bak);
        }
    }

    public function test_lista_actividades_sacd_falta_sacd_devuelve_mostrar_nota_true(): void
    {
        $out = ListaActividadesSacdData::execute([
            'tipo' => 'falta_sacd',
            'year' => '2099',
            'periodo' => 'tot_any',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('falta_sacd', $out['tipo']);
        $this->assertTrue($out['mostrar_nota_falta_sacd']);
        $this->assertIsArray($out['filas']);
        $this->assertIsString($out['texto_fase_ok_sacd']);
    }

    public function test_solapes_sacd_periodo_futuro_devuelve_estructura(): void
    {
        $out = SolapesSacdData::execute([
            'year' => '2099',
            'periodo' => 'tot_any',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertArrayHasKey('titulo', $out);
        $this->assertArrayHasKey('inicio_iso', $out);
        $this->assertArrayHasKey('fin_iso', $out);
        $this->assertArrayHasKey('texto_fase_ok_sacd', $out);
        $this->assertArrayHasKey('filas', $out);
        $this->assertIsArray($out['filas']);
    }

    public function test_sacd_asignar_auto_sin_fecha_no_asigna_nada(): void
    {
        $out = SacdAsignarAuto::execute(['f_ini_iso' => '']);

        $this->assertSame(['asignadas' => 0, 'sin_asignar' => 0], $out);
    }

    public function test_sacd_asignar_auto_fecha_futura_no_asigna(): void
    {
        $out = SacdAsignarAuto::execute(['f_ini_iso' => '2099-12-31']);

        $this->assertArrayHasKey('asignadas', $out);
        $this->assertArrayHasKey('sin_asignar', $out);
        $this->assertSame(0, $out['asignadas']);
        $this->assertSame(0, $out['sin_asignar']);
    }

    public function test_comunicacion_actividades_sacd_data_un_sacd_inexistente_devuelve_estructura(): void
    {
        $out = ComunicacionActividadesSacdData::execute([
            'que' => 'un_sacd',
            'id_nom' => 999999999,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertArrayHasKey('que', $out);
        $this->assertArrayHasKey('propuesta', $out);
        $this->assertArrayHasKey('mi_dele', $out);
        $this->assertArrayHasKey('lugar_fecha', $out);
        $this->assertArrayHasKey('periodo_txt', $out);
        $this->assertArrayHasKey('sacds', $out);
        $this->assertArrayHasKey('sacds_paso', $out);
        $this->assertIsArray($out['sacds']);
        $this->assertIsArray($out['sacds_paso']);
        // un_sacd nunca tiene sacds de paso.
        $this->assertSame([], $out['sacds_paso']);
    }

    public function test_comunicacion_actividades_sacd_data_resolver_contexto_un_sacd_aplica_periodo_cruzado(): void
    {
        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => 'un_sacd',
            'id_nom' => 111,
            'propuesta' => '',
            'periodo' => '',
            'year' => '2030',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('un_sacd', $ctx['que']);
        $this->assertSame('2029-07-01', $ctx['inicioIso']);
        $this->assertSame('2031-06-30', $ctx['finIso']);
        $this->assertIsString($ctx['periodo_txt']);
    }

    public function test_comunicacion_actividades_sacd_data_resolver_contexto_sel_fuerza_un_sacd(): void
    {
        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => '',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => '',
            'year' => '2030',
            'empiezamin' => '',
            'empiezamax' => '',
            'sel' => ['555#n'],
        ]);

        $this->assertSame('un_sacd', $ctx['que']);
        $this->assertSame(555, $ctx['id_nom']);
    }

    public function test_comunicacion_actividades_sacd_data_resolver_contexto_default_nagd(): void
    {
        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => '',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('nagd', $ctx['que']);
        $this->assertSame(0, $ctx['id_nom']);
        // `tot_any` en Periodo usa formato `Y/m/d`; basta con validar que
        // contiene el year pedido.
        $this->assertStringContainsString('2099', (string)$ctx['inicioIso']);
        $this->assertStringContainsString('2099', (string)$ctx['finIso']);
    }

    public function test_comunicacion_actividades_sacd_enviar_devuelve_string(): void
    {
        $out = ComunicacionActividadesSacdEnviar::execute([
            'que' => 'un_sacd',
            'id_nom' => 999999999,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertIsString($out);
    }

    public function test_helper_getLugar_dl_devuelve_string(): void
    {
        $helper = new ActividadesSacdHelper();
        $this->assertIsString($helper->getLugar_dl());
    }

    public function test_helper_getTraduccion_clave_desconocida_devuelve_string(): void
    {
        $helper = new ActividadesSacdHelper();
        $txt = $helper->getTraduccion('__clave_que_no_existe__', 'es');
        $this->assertIsString($txt);
        $this->assertStringContainsString('__clave_que_no_existe__', $txt);
    }

    public function test_helper_getArrayTraducciones_devuelve_array_o_false(): void
    {
        $helper = new ActividadesSacdHelper();
        $out = $helper->getArrayTraducciones('es');
        $this->assertTrue(is_array($out) || $out === false);
    }

    public function test_comunicar_actividades_service_sin_personas_devuelve_array_vacio(): void
    {
        $service = new ComunicarActividadesSacdService();
        $service->setInicioIso('2099-01-01');
        $service->setFinIso('2099-12-31');
        $service->setPropuesta('');
        $service->setPersonas([]);

        $this->assertSame([], $service->getArrayComunicacion());
    }

    public function test_comunicar_actividades_service_enviarmails_lista_vacia_devuelve_string(): void
    {
        $service = new ComunicarActividadesSacdService();
        $out = $service->enviarMails([]);
        $this->assertIsString($out);
    }

    public static function providerProcesosInstalado(): \Generator
    {
        yield 'procesos no instalado' => [false];
        yield 'procesos instalado' => [true];
    }

    /**
     * @return array{a_apps: array, app_installed: array}
     */
    private function backupSessionAppsConfig(): array
    {
        return [
            'a_apps' => $_SESSION['config']['a_apps'] ?? [],
            'app_installed' => $_SESSION['config']['app_installed'] ?? [],
        ];
    }

    /**
     * @param array{a_apps: array, app_installed: array} $bak
     */
    private function restoreSessionAppsConfig(array $bak): void
    {
        $_SESSION['config']['a_apps'] = $bak['a_apps'];
        $_SESSION['config']['app_installed'] = $bak['app_installed'];
    }

    private function applySessionAppInstalled(string $nomApp, bool $install, int $fakeId): void
    {
        $aApps = $_SESSION['config']['a_apps'] ?? [];
        $installed = $_SESSION['config']['app_installed'] ?? [];
        if ($install) {
            $aApps[$nomApp] = $fakeId;
            $_SESSION['config']['a_apps'] = $aApps;
            $_SESSION['config']['app_installed'] = array_values(array_unique(array_merge($installed, [$fakeId])));

            return;
        }
        $rmId = $aApps[$nomApp] ?? null;
        unset($aApps[$nomApp]);
        $next = $installed;
        if ($rmId !== null) {
            $next = array_values(array_diff($next, [$rmId]));
        }
        $next = array_values(array_diff($next, [$fakeId]));
        $_SESSION['config']['a_apps'] = $aApps;
        $_SESSION['config']['app_installed'] = $next;
    }

    private function refreshPermActividadesSession(): void
    {
        if (ConfigGlobal::is_app_installed('procesos')) {
            $_SESSION['oPermActividades'] = new PermisosActividades(ConfigGlobal::mi_id_usuario());
        } else {
            $_SESSION['oPermActividades'] = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        }
    }
}
