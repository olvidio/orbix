<?php

namespace Tests\unit\configuracion\domain;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use src\configuracion\domain\entity\ModuloInstalado;
use src\configuracion\domain\ModulosConfig;
use src\configuracion\domain\value_objects\AppsReq;
use src\configuracion\domain\value_objects\ModsReq;
use src\configuracion\domain\value_objects\ModuloId;
use src\configuracion\domain\value_objects\ModuloName;
use Tests\myTest;

class ModulosConfigTest extends myTest
{
    /**
     * @param list<ModuloInstalado> $instalados
     */
    private function buildConfig(array $instalados): ModulosConfig
    {
        $procesos = new Modulo();
        $procesos->setIdModVo(new ModuloId(10));
        $procesos->setNomVo(new ModuloName('procesos'));
        $procesos->setModsReqVo(new ModsReq([]));
        $procesos->setAppsReqVo(new AppsReq([100]));

        $cambios = new Modulo();
        $cambios->setIdModVo(new ModuloId(20));
        $cambios->setNomVo(new ModuloName('cambios'));
        $cambios->setModsReqVo(new ModsReq([10]));
        $cambios->setAppsReqVo(new AppsReq([200]));

        $moduloRepository = $this->createMock(ModuloRepositoryInterface::class);
        $moduloRepository->method('getModulos')->willReturn([$procesos, $cambios]);

        $appRepository = $this->createMock(AppRepositoryInterface::class);
        $appRepository->method('getApps')->willReturn([]);

        $moduloInstaladoRepository = $this->createMock(ModuloInstaladoRepositoryInterface::class);
        $moduloInstaladoRepository->method('getModuloInstalados')->willReturn($instalados);

        return new ModulosConfig($moduloRepository, $appRepository, $moduloInstaladoRepository);
    }

    private function instalado(int $id_mod, bool $active): ModuloInstalado
    {
        $mod = new ModuloInstalado();
        $mod->setIdModVo(new ModuloId($id_mod));
        $mod->setActive($active);

        return $mod;
    }

    public function test_get_apps_mods_no_incluye_apps_de_dependencia_inactiva(): void
    {
        $config = $this->buildConfig([
            $this->instalado(10, false),
            $this->instalado(20, true),
        ]);

        $activos = $config->getModsInstaladosActivos();
        $this->assertSame(['cambios'], array_values($activos));

        $appsCambios = $config->getAppsMods(20, $activos);
        $this->assertSame([200], $appsCambios);
        $this->assertNotContains(100, $appsCambios);
    }

    public function test_get_apps_instaladas_excluye_apps_de_modulos_inactivos(): void
    {
        $config = $this->buildConfig([
            $this->instalado(10, false),
            $this->instalado(20, true),
        ]);

        $this->assertSame([200], $config->getAppsInstaladas());
    }

    public function test_get_modulos_activos_que_requieren(): void
    {
        $config = $this->buildConfig([
            $this->instalado(10, true),
            $this->instalado(20, true),
        ]);

        $dependientes = $config->getModulosActivosQueRequieren(10);
        $this->assertSame([20 => 'cambios'], $dependientes);
    }

    public function test_get_modulos_activos_que_requieren_ignora_dependiente_inactivo(): void
    {
        $config = $this->buildConfig([
            $this->instalado(10, true),
            $this->instalado(20, false),
        ]);

        $this->assertSame([], $config->getModulosActivosQueRequieren(10));
    }
}
