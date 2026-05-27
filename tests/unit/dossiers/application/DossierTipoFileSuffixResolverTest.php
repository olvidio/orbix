<?php

declare(strict_types=1);

namespace Tests\unit\dossiers\application;

use PHPUnit\Framework\TestCase;
use src\dossiers\application\DossierTipoFileSuffixResolver;
use src\dossiers\domain\entity\TipoDossier;

final class DossierTipoFileSuffixResolverTest extends TestCase
{
    private string $tmpRoot = '';

    protected function tearDown(): void
    {
        if ($this->tmpRoot !== '' && is_dir($this->tmpRoot)) {
            $this->rrmdir($this->tmpRoot);
            $this->tmpRoot = '';
        }
        parent::tearDown();
    }

    public function test_sin_codigo_usa_id_numerico(): void
    {
        $resolver = new DossierTipoFileSuffixResolver(sys_get_temp_dir());
        $tipo = new TipoDossier();
        $tipo->setId_tipo_dossier(1302);
        $tipo->setApp('algo');
        $tipo->setCodigoVo(null);

        $this->assertSame('1302', $resolver->resolveSuffix($tipo, DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER));
    }

    public function test_con_codigo_sin_fichero_cae_a_id(): void
    {
        $this->tmpRoot = sys_get_temp_dir() . '/orbix_dossier_suffix_' . bin2hex(random_bytes(4));
        mkdir($this->tmpRoot . '/apps/xmod/controller', 0777, true);

        $resolver = new DossierTipoFileSuffixResolver($this->tmpRoot);
        $tipo = new TipoDossier();
        $tipo->setId_tipo_dossier(99);
        $tipo->setApp('xmod');
        $tipo->setCodigoVo('nohay');

        $this->assertSame('99', $resolver->resolveSuffix($tipo, DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER));
    }

    public function test_con_codigo_y_fichero_legacy_usa_codigo(): void
    {
        $this->tmpRoot = sys_get_temp_dir() . '/orbix_dossier_suffix_' . bin2hex(random_bytes(4));
        mkdir($this->tmpRoot . '/apps/ymod/controller', 0777, true);
        touch($this->tmpRoot . '/apps/ymod/controller/form_abc.php');

        $resolver = new DossierTipoFileSuffixResolver($this->tmpRoot);
        $tipo = new TipoDossier();
        $tipo->setId_tipo_dossier(5);
        $tipo->setApp('ymod');
        $tipo->setCodigoVo('abc');

        $this->assertSame('abc', $resolver->resolveSuffix($tipo, DossierTipoFileSuffixResolver::KIND_FORM_CONTROLLER));
    }

    public function test_selectBaseClassName(): void
    {
        $resolver = new DossierTipoFileSuffixResolver(sys_get_temp_dir());
        $this->assertSame('Select1302', $resolver->selectBaseClassName('1302'));
        $this->assertSame('Select_cargos', $resolver->selectBaseClassName('cargos'));
    }

    public function test_resolveSelectClassFqcn_ubiscamas_habitaciones_cdc(): void
    {
        $resolver = DossierTipoFileSuffixResolver::fromDefaultProjectRoot();
        $tipo = new TipoDossier();
        $tipo->setId_tipo_dossier(2006);
        $tipo->setApp('ubiscamas');
        $tipo->setDbVo(5);
        $tipo->setCodigoVo('habitaciones_cdc');

        $this->assertSame(
            'src\\ubiscamas\\domain\\Select_habitaciones_cdc',
            $resolver->resolveSelectClassFqcn($tipo)
        );
    }

    private function rrmdir(string $dir): void
    {
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->rrmdir($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
