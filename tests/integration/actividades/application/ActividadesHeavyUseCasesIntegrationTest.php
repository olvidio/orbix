<?php

namespace Tests\integration\actividades\application;

use src\actividades\application\ActividadNueva;
use src\actividades\application\ActividadNuevoCurso;
use src\actividades\application\ActividadNuevoCursoEjecutar;
use src\actividades\application\ActividadSelectListado;
use src\actividades\application\ActividadVerDatos;
use src\actividades\application\ListaActividadesSgListado;
use src\actividades\application\ListaSrCsvListado;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\shared\config\ConfigGlobal;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use Tests\myTest;

/**
 * Smoke / contrato de salida para casos de uso con muchas dependencias ($GLOBALS,
 * repos, Ubi, TiposActividades, procesos…). Complementa unitarios sin montar DI.
 *
 * Donde el comportamiento depende de {@see ConfigGlobal::is_app_installed()}, los tests
 * usan data providers para cubrir explícitamente app instalada y no instalada; al togglear
 * `procesos` se restaura también `$_SESSION['oPermActividades']` como en {@see myTest}.
 */
class ActividadesHeavyUseCasesIntegrationTest extends myTest
{
    public function test_actividad_ver_datos_sin_id_devuelve_payload_esperado(): void
    {
        $out = (new ActividadVerDatos())->ejecutar([
            'id_activ' => 0,
            'isfsv' => 1,
            'Bdl' => 't',
            'dl_org' => ConfigGlobal::mi_delef(),
            'calc_tarifa_inicial' => false,
        ]);

        $this->assertArrayHasKey('entidad', $out);
        $this->assertNull($out['entidad']);
        $this->assertArrayHasKey('html_despl_dl_org', $out);
        $this->assertArrayHasKey('html_despl_tarifa', $out);
        $this->assertArrayHasKey('html_despl_nivel_stgr', $out);
        $this->assertArrayHasKey('html_despl_idioma', $out);
        $this->assertArrayHasKey('html_despl_repeticion', $out);
        $this->assertArrayHasKey('nombre_ubi', $out);
        $this->assertIsString($out['html_despl_dl_org']);
    }

    public function test_lista_sr_csv_listado_devuelve_estructura(): void
    {
        $out = (new ListaSrCsvListado())->ejecutar([
            'periodo' => 'curso_ca',
            'year' => '',
            'dl_org' => '',
            'empiezamin' => '',
            'empiezamax' => '',
            'c_activ' => [1],
            'status' => [1, 2],
            'id_cdc' => [],
        ]);

        $this->assertArrayHasKey('html_tabla', $out);
        $this->assertArrayHasKey('a_cabeceras', $out);
        $this->assertArrayHasKey('a_valores', $out);
        $this->assertArrayHasKey('titulo', $out);
        $this->assertArrayHasKey('pref_error', $out);
        $this->assertIsArray($out['a_cabeceras']);
        $this->assertIsArray($out['a_valores']);
    }

    /**
     * @dataProvider providerProcesosInstalado
     */
    public function test_lista_actividades_sg_listado_sin_filas_por_id_ubi_imposible(bool $procesosInstalado): void
    {
        $bak = $this->backupSessionAppsConfig();
        $permBak = $_SESSION['oPermActividades'];
        $this->applySessionAppInstalled('procesos', $procesosInstalado, 989701);
        $this->refreshPermActividadesSession();
        $this->assertSame($procesosInstalado, ConfigGlobal::is_app_installed('procesos'));

        try {
            $out = (new ListaActividadesSgListado())->ejecutar([
                'continuar' => '',
                'status' => 2,
                'tipo_activ_sg' => 'crt',
                'id_ubi' => 999999999,
                'periodo' => 'actual',
                'year' => '',
                'dl_org' => '',
                'empiezamin' => '',
                'empiezamax' => '',
                'sel' => [],
                'scroll_id' => '',
            ], 0);

            $this->assertArrayNotHasKey('html_tabla', $out);
            $this->assertArrayHasKey('a_cabeceras', $out);
            $this->assertArrayHasKey('a_valores', $out);
            $this->assertArrayHasKey('result_busqueda', $out);
            $this->assertArrayHasKey('id_tipo_activ', $out);
            $this->assertArrayHasKey('html_advertencia', $out);
            $this->assertSame('', $out['html_advertencia']);
        } finally {
            $_SESSION['oPermActividades'] = $permBak;
            $this->restoreSessionAppsConfig($bak);
        }
    }

    /**
     * @dataProvider providerProcesosInstalado
     */
    public function test_actividad_select_listado_sin_filas_por_id_ubi_imposible(bool $procesosInstalado): void
    {
        $bak = $this->backupSessionAppsConfig();
        $permBak = $_SESSION['oPermActividades'];
        $this->applySessionAppInstalled('procesos', $procesosInstalado, 989701);
        $this->refreshPermActividadesSession();
        $this->assertSame($procesosInstalado, ConfigGlobal::is_app_installed('procesos'));

        try {
            $out = (new ActividadSelectListado())->ejecutar([
                'modo' => '',
                'continuar' => '',
                'status' => 2,
                'id_tipo_activ' => '......',
                'filtro_lugar' => '',
                'id_ubi' => 999999999,
                'nom_activ' => '',
                'periodo' => 'actual',
                'year' => '',
                'dl_org' => '',
                'empiezamin' => '',
                'empiezamax' => '',
                'fases_on' => [],
                'fases_off' => [],
                'publicado' => 0,
                'ssfsv' => 'sv',
                'sasistentes' => '.',
                'sactividad' => '.',
                'sactividad2' => '',
                'sel' => [],
                'scroll_id' => '',
            ], 0);

            $this->assertArrayNotHasKey('html_tabla', $out);
            $this->assertArrayHasKey('a_cabeceras', $out);
            $this->assertArrayHasKey('a_valores', $out);
            $this->assertArrayHasKey('resultado', $out);
            $this->assertArrayHasKey('perm_nueva', $out);
            $this->assertArrayHasKey('mod', $out);
            $this->assertArrayHasKey('obj_pau', $out);
            $this->assertArrayHasKey('aTiposActiv', $out);
            $this->assertSame('', $out['html_advertencia']);
        } finally {
            $_SESSION['oPermActividades'] = $permBak;
            $this->restoreSessionAppsConfig($bak);
        }
    }

    public function test_actividad_nueva_campos_obligatorios_vacios_lanza(): void
    {
        $this->expectException(\RuntimeException::class);

        ActividadNueva::actividadNueva([
            'dl_org' => ConfigGlobal::mi_delef(),
            'publicado' => false,
            'id_tipo_activ' => '111111',
            'nom_activ' => '',
            'f_ini' => '2020-01-01',
            'f_fin' => '2020-12-31',
            'status' => 1,
            'id_ubi' => 0,
            'lugar_esp' => '',
            'desc_activ' => '',
            'precio' => null,
            'num_asistentes' => null,
            'observ' => '',
            'nivel_stgr' => '',
            'id_repeticion' => 0,
            'observ_material' => '',
            'tarifa' => null,
            'h_ini' => '',
            'h_fin' => '',
            'plazas' => null,
        ]);
    }

    /**
     * Cubre getNewId / getNewIdActividad + Guardar (el test de campos vacíos sale antes).
     * Tipo 271000 y dl_org alineados con {@see \Tests\factories\actividades\ActividadAllFactory::createSimple()}.
     */
    public function test_actividad_nueva_dl_genera_ids_y_guarda(): void
    {
        $repo = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $idTipo = 271000;
        $dlOrg = ConfigGlobal::mi_delef(substr((string) $idTipo, 0, 1));
        $nom = 'act_nueva_it_' . uniqid('', true);

        $idActiv = 0;
        try {
            $idActiv = (int) ActividadNueva::actividadNueva([
                'dl_org' => $dlOrg,
                'publicado' => false,
                'id_tipo_activ' => $idTipo,
                'nom_activ' => $nom,
                'f_ini' => '1/6/2099',
                'f_fin' => '2/6/2099',
                'status' => StatusId::PROYECTO,
                'id_ubi' => 0,
                'lugar_esp' => '',
                'desc_activ' => '',
                'precio' => null,
                'num_asistentes' => null,
                'observ' => '',
                'nivel_stgr' => NivelStgrId::N,
                'id_repeticion' => 1,
                'observ_material' => '',
                'tarifa' => null,
                'h_ini' => '',
                'h_fin' => '',
                'plazas' => null,
            ]);
            $this->assertGreaterThan(0, $idActiv);
            $guardada = $repo->findById($idActiv);
            $this->assertNotNull($guardada);
            $this->assertSame($nom, $guardada->getNom_activ());
        } finally {
            if ($idActiv > 0) {
                $borrar = $repo->findById($idActiv);
                if ($borrar !== null) {
                    $repo->Eliminar($borrar);
                }
            }
        }
    }

    /**
     * @dataProvider providerActividadPlazasInstalada
     */
    public function test_actividad_nueva_dl_plazas_respetan_app_actividadplazas(bool $appInstalada, ?int $plazasEsperadasEnRepo): void
    {
        $bak = $this->backupSessionAppsConfig();
        $this->applySessionAppInstalled('actividadplazas', $appInstalada, 989898);
        $this->assertSame($appInstalada, ConfigGlobal::is_app_installed('actividadplazas'));

        $repo = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        $repoPlazas = $GLOBALS['container']->get(ActividadPlazasDlRepositoryInterface::class);
        $idTipo = 271000;
        $dlOrg = ConfigGlobal::mi_delef(substr((string) $idTipo, 0, 1));
        $nom = 'act_nueva_plz_' . uniqid('', true);
        $idActiv = 0;

        try {
            $idActiv = (int) ActividadNueva::actividadNueva([
                'dl_org' => $dlOrg,
                'publicado' => false,
                'id_tipo_activ' => $idTipo,
                'nom_activ' => $nom,
                'f_ini' => '3/6/2099',
                'f_fin' => '4/6/2099',
                'status' => StatusId::PROYECTO,
                'id_ubi' => 0,
                'lugar_esp' => '',
                'desc_activ' => '',
                'precio' => null,
                'num_asistentes' => null,
                'observ' => '',
                'nivel_stgr' => NivelStgrId::N,
                'id_repeticion' => 1,
                'observ_material' => '',
                'tarifa' => null,
                'h_ini' => '',
                'h_fin' => '',
                'plazas' => 4,
            ]);
            $this->assertGreaterThan(0, $idActiv);
            $rowPlazas = $repoPlazas->findById($idActiv);
            if ($plazasEsperadasEnRepo === null) {
                $this->assertNull($rowPlazas);
            } else {
                $this->assertNotNull($rowPlazas);
                $this->assertSame($plazasEsperadasEnRepo, $rowPlazas->getPlazasVo()?->value());
            }
        } finally {
            $this->restoreSessionAppsConfig($bak);
            if ($idActiv > 0) {
                $pz = $repoPlazas->findById($idActiv);
                if ($pz !== null) {
                    $repoPlazas->Eliminar($pz);
                }
                $borrar = $repo->findById($idActiv);
                if ($borrar !== null) {
                    $repo->Eliminar($borrar);
                }
            }
        }
    }

    public function test_actividad_nuevo_curso_comprobar_solapes_devuelve_string(): void
    {
        $uc = new ActividadNuevoCurso();
        $txt = $uc->comprobar_solapes('2090-01-01', '2090-12-31');
        $this->assertIsString($txt);
    }

    /**
     * @dataProvider providerProcesosInstalado
     */
    public function test_actividad_nuevo_curso_ejecutar_anios_lejanos_sin_copias(bool $procesosInstalado): void
    {
        $bak = $this->backupSessionAppsConfig();
        $permBak = $_SESSION['oPermActividades'];
        $this->applySessionAppInstalled('procesos', $procesosInstalado, 989702);
        $this->refreshPermActividadesSession();
        $this->assertSame($procesosInstalado, ConfigGlobal::is_app_installed('procesos'));

        try {
            $out = (new ActividadNuevoCursoEjecutar())->ejecutar([
                'year_ref' => 2089,
                'year' => 2090,
                'ver_lista' => false,
            ]);

            $this->assertArrayHasKey('html', $out);
            $this->assertArrayHasKey('copiadas', $out);
            $this->assertIsString($out['html']);
            $this->assertIsInt($out['copiadas']);
            $this->assertSame(0, $out['copiadas']);
        } finally {
            $_SESSION['oPermActividades'] = $permBak;
            $this->restoreSessionAppsConfig($bak);
        }
    }

    public static function providerProcesosInstalado(): \Generator
    {
        yield 'procesos no instalado' => [false];
        yield 'procesos instalado' => [true];
    }

    public static function providerActividadPlazasInstalada(): \Generator
    {
        yield 'actividadplazas no instalada' => [false, null];
        yield 'actividadplazas instalada' => [true, 4];
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
