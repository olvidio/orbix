<?php

declare(strict_types=1);

namespace Tests\unit\shared\config;

use PHPUnit\Framework\TestCase;
use src\shared\config\ConfigMagik;

final class ConfigMagikTest extends TestCase
{
    private string $tmpFile = '';

    protected function tearDown(): void
    {
        if ($this->tmpFile !== '' && is_file($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    public function test_roundtrip_parentesis_apostrofes_y_comillas(): void
    {
        $texto = "<span title=\"Delegación (dlb)\">l'escola d'Ausiàs</span>\nsegunda línea (pie)";
        $cfg = $this->newConfig();
        $cfg->set('cabecera', $texto, 'texto_tipo');
        $cfg->save($this->tmpFile);

        $reloaded = new ConfigMagik($this->tmpFile, false, true);
        $this->assertSame($texto, $reloaded->get('cabecera', 'texto_tipo'));
        $this->assertStringContainsString('\\"', (string) file_get_contents($this->tmpFile));
    }

    public function test_carga_ini_roto_por_comillas_html_y_parentesis(): void
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'cfgmagik');
        file_put_contents($this->tmpFile, <<<'INI'
<?PHP
; /*
; -- BEGIN PROTECTED_MODE
[texto_tipo]
cabecera = "<span title="Delegación (dlb)">x</span>"
pie = "Carpeta d'impresos (casa)
segunda linea"
; -- END PROTECTED_MODE
; */
 ?>
    mm
INI);

        $cfg = new ConfigMagik($this->tmpFile, false, true);
        $this->assertSame('<span title="Delegación (dlb)">x</span>', $cfg->get('cabecera', 'texto_tipo'));
        $this->assertSame("Carpeta d'impresos (casa)\nsegunda linea", $cfg->get('pie', 'texto_tipo'));
    }

    public function test_carga_dist_de_cabecera_pie(): void
    {
        $dist = realpath(__DIR__ . '/../../../../data/inventario/cabecera_pie_textos.ini.dist');
        $this->assertNotFalse($dist);
        $this->assertFileExists($dist);

        $cfg = new ConfigMagik($dist, false, true);
        $cabecera = $cfg->get('cabecera', 'texto_tipo');
        $this->assertIsString($cabecera);
        $this->assertStringContainsString("l'inventari", $cabecera);
        $pie = $cfg->get('pie', 'texto_tipo');
        $this->assertIsString($pie);
        $this->assertStringContainsString("d'impresos", $pie);
        $this->assertStringContainsString("L'ARMARI", $pie);
    }

    private function newConfig(): ConfigMagik
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'cfgmagik');
        file_put_contents($this->tmpFile, '');

        return new ConfigMagik($this->tmpFile, false, true);
    }
}
