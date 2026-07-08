<?php

declare(strict_types=1);

namespace Tests\integration\cambios\application;

use src\cambios\application\RegistrarCambio;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use Tests\myTest;

final class RegistrarCambioIntegracionTest extends myTest
{
    /** @var array{a_apps: mixed, app_installed: mixed} */
    private array $configAppsBackup = [];

    public function setUp(): void
    {
        if (!is_string($_SESSION['session_auth']['esquema'] ?? null)
            || $_SESSION['session_auth']['esquema'] === ''
        ) {
            unset($_SESSION['session_auth']);
        }

        parent::setUp();
        $this->configAppsBackup = [
            'a_apps' => $_SESSION['config']['a_apps'] ?? [],
            'app_installed' => $_SESSION['config']['app_installed'] ?? [],
        ];
    }

    public function tearDown(): void
    {
        $_SESSION['config']['a_apps'] = $this->configAppsBackup['a_apps'];
        $_SESSION['config']['app_installed'] = $this->configAppsBackup['app_installed'];
        parent::tearDown();
    }

    public function test_con_cambios_instalados_guarda_en_av_cambios_dl(): void
    {
        $this->instalarApp('cambios', 99002);

        /** @var RegistrarCambio $registrar */
        $registrar = $GLOBALS['container']->get(RegistrarCambio::class);
        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);

        $idActiv = 990012345;
        $datosBase = [
            'id_tipo_activ' => 112401,
            'status' => 4,
            'dl_org' => 'dlb',
            'nom_activ' => 'Integracion test',
        ];

        $registrar->execute(
            'ActividadDl',
            'UPDATE',
            $idActiv,
            $datosBase + [
                'f_ini' => new DateTimeLocal('2026-07-07'),
                'h_ini' => TimeLocal::fromString('11:00:00'),
            ],
            $datosBase + [
                'f_ini' => new DateTimeLocal('2026-01-01'),
                'h_ini' => TimeLocal::fromString('09:30:00'),
            ],
        );

        $creados = $this->filtrarCambiosNuevosPorActiv($idActiv);
        $this->assertCount(2, $creados);

        $propiedades = array_map(
            static fn (Cambio $cambio): string => (string) $cambio->getPropiedad(),
            $creados,
        );
        sort($propiedades);
        $this->assertSame(['f_ini', 'h_ini'], $propiedades);

        foreach ($creados as $cambio) {
            $this->assertSame('ActividadDl', $cambio->getObjeto());
            $this->assertSame('dlb', $cambio->getDl_org());
            $persistido = $dlRepository->findById($cambio->getId_item_cambio());
            $this->assertNotNull($persistido);
            $this->assertTrue($dlRepository->Eliminar($persistido));
        }
    }

    public function test_sin_cambios_instalados_guarda_en_av_cambios_public(): void
    {
        $_SESSION['config']['a_apps'] = [];
        $_SESSION['config']['app_installed'] = [];

        /** @var RegistrarCambio $registrar */
        $registrar = $GLOBALS['container']->get(RegistrarCambio::class);
        /** @var CambioRepositoryInterface $cambioRepository */
        $cambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);

        try {
            $probeId = (int) $cambioRepository->getNewId();
        } catch (\PDOException) {
            $this->markTestSkipped('public.av_cambios sin secuencia en este entorno de test');
        }
        if ($probeId <= 0) {
            $this->markTestSkipped('public.av_cambios sin secuencia en este entorno de test');
        }

        $idActiv = 990012346;
        $datosBase = [
            'id_tipo_activ' => 112401,
            'status' => 4,
            'dl_org' => 'dlb',
            'nom_activ' => 'Integracion public',
        ];

        $registrar->execute(
            'Actividad',
            'UPDATE',
            $idActiv,
            array_merge($datosBase, ['nom_activ' => 'Nombre nuevo']),
            array_merge($datosBase, ['nom_activ' => 'Integracion public']),
        );

        $creados = $this->filtrarCambiosNuevosPorActiv($idActiv);
        $this->assertCount(1, $creados);
        $this->assertSame('nom_activ', $creados[0]->getPropiedad());
        $this->assertSame('Integracion public', $creados[0]->getValor_old());
        $this->assertSame('Nombre nuevo', $creados[0]->getValor_new());

        $persistido = $cambioRepository->findById($creados[0]->getId_item_cambio());
        $this->assertNotNull($persistido);
        $this->assertTrue($cambioRepository->Eliminar($persistido));
    }

    private function instalarApp(string $nombre, int $idApp): void
    {
        $aApps = $_SESSION['config']['a_apps'] ?? [];
        if (!is_array($aApps)) {
            $aApps = [];
        }
        $aApps[$nombre] = $idApp;
        $_SESSION['config']['a_apps'] = $aApps;

        $installed = $_SESSION['config']['app_installed'] ?? [];
        if (!is_array($installed)) {
            $installed = [];
        }
        if (!in_array($idApp, $installed, true)) {
            $installed[] = $idApp;
        }
        $_SESSION['config']['app_installed'] = $installed;
    }

    /**
     * @return list<Cambio>
     */
    private function filtrarCambiosNuevosPorActiv(int $idActiv): array
    {
        /** @var CambioRepositoryInterface $cambioRepository */
        $cambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);

        $result = [];
        foreach ($cambioRepository->getCambiosNuevos() as $cambio) {
            if ($cambio->getId_activ() === $idActiv) {
                $result[] = $cambio;
            }
        }

        return $result;
    }
}
