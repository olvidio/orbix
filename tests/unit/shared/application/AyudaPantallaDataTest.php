<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaPantallaData;

final class AyudaPantallaDataTest extends TestCase
{
    private const CTRL = 'frontend/actividades/controller/actividad_ver.php';

    private string $docsRoot;

    protected function setUp(): void
    {
        $this->docsRoot = sys_get_temp_dir() . '/orbix_ayuda_pantalla_' . bin2hex(random_bytes(4));
        mkdir($this->docsRoot . '/manual', 0777, true);
        file_put_contents($this->docsRoot . '/manual/actividades.md', "# Manual\n");
        file_put_contents($this->docsRoot . '/manual/_indice_pantallas.json', json_encode([
            'version' => 1,
            'pantallas' => [
                self::CTRL => [
                    'titulo' => 'Ficha de actividad',
                    'modulo' => 'actividades',
                    'manual' => [
                        ['modulo' => 'actividades', 'ancla' => 'crear-una-actividad', 'titulo' => 'Crear una actividad', 'sirve' => 'Dar de alta.'],
                        ['modulo' => 'actividades', 'ancla' => 'modificar-una-actividad', 'titulo' => 'Modificar una actividad', 'sirve' => 'Cambiar datos.'],
                        ['modulo' => 'actividades', 'ancla' => 'duplicar-una-actividad', 'titulo' => 'Duplicar una actividad', 'sirve' => ''],
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));
    }

    protected function tearDown(): void
    {
        foreach (glob($this->docsRoot . '/manual/*') ?: [] as $f) {
            unlink($f);
        }
        rmdir($this->docsRoot . '/manual');
        rmdir($this->docsRoot);
    }

    private function useCase(): AyudaPantallaData
    {
        return new AyudaPantallaData($this->docsRoot);
    }

    public function test_pantalla_con_ayuda_devuelve_tareas_en_orden(): void
    {
        $r = $this->useCase()->execute(['controller' => self::CTRL]);

        self::assertTrue($r['encontrada']);
        self::assertSame(self::CTRL, $r['controller']);
        self::assertSame('Ficha de actividad', $r['titulo']);
        self::assertSame('actividades', $r['modulo']);
        self::assertSame('actividades', $r['manual_modulo']);
        self::assertSame(3, $r['total_tareas']);
        self::assertSame(
            ['crear-una-actividad', 'modificar-una-actividad', 'duplicar-una-actividad'],
            array_column($r['tareas'], 'ancla'),
        );
        self::assertSame('', $r['aviso_txt']);
    }

    public function test_limite_recorta_tareas_pero_informa_del_total(): void
    {
        $r = $this->useCase()->execute(['controller' => self::CTRL, 'limite' => '2']);

        self::assertCount(2, $r['tareas']);
        self::assertSame(3, $r['total_tareas']);
    }

    public function test_limite_invalido_usa_el_por_defecto(): void
    {
        $r = $this->useCase()->execute(['controller' => self::CTRL, 'limite' => -5]);

        self::assertCount(3, $r['tareas']);
    }

    public function test_acepta_url_completa_con_query_y_hash(): void
    {
        $url = 'https://orbix.example/' . self::CTRL . '?id_activ=3&h=abc#x';

        $r = $this->useCase()->execute(['controller' => $url]);

        self::assertTrue($r['encontrada']);
        self::assertSame(self::CTRL, $r['controller']);
    }

    public function test_pantalla_sin_entrada_no_es_error_y_ofrece_el_manual_del_modulo(): void
    {
        $r = $this->useCase()->execute(['controller' => 'frontend/actividades/controller/otra_pantalla.php']);

        self::assertFalse($r['encontrada']);
        self::assertSame('actividades', $r['modulo']);
        self::assertSame('actividades', $r['manual_modulo']);
        self::assertSame([], $r['tareas']);
        self::assertNotSame('', $r['aviso_txt']);
    }

    public function test_pantalla_sin_entrada_y_sin_manual_de_modulo(): void
    {
        $r = $this->useCase()->execute(['controller' => 'frontend/inexistente/controller/x.php']);

        self::assertFalse($r['encontrada']);
        self::assertSame('', $r['manual_modulo']);
    }

    public function test_controller_vacio_o_no_valido_no_encuentra_nada(): void
    {
        foreach (['', '   ', '../../etc/passwd', 'frontend/../controller/x.php', 'src/shared/x.php'] as $valor) {
            $r = $this->useCase()->execute(['controller' => $valor]);

            self::assertFalse($r['encontrada'], $valor);
            self::assertSame('', $r['controller'], $valor);
            self::assertSame('', $r['manual_modulo'], $valor);
        }
        self::assertFalse($this->useCase()->execute([])['encontrada']);
    }

    public function test_sin_indice_devuelve_sin_ayuda(): void
    {
        unlink($this->docsRoot . '/manual/_indice_pantallas.json');

        $r = $this->useCase()->execute(['controller' => self::CTRL]);

        self::assertFalse($r['encontrada']);
        self::assertSame('actividades', $r['manual_modulo']);
    }

    public function test_indice_corrupto_se_trata_como_vacio(): void
    {
        file_put_contents($this->docsRoot . '/manual/_indice_pantallas.json', '{no es json');

        self::assertFalse($this->useCase()->execute(['controller' => self::CTRL])['encontrada']);
    }

    public function test_ignora_entradas_de_tarea_incompletas(): void
    {
        file_put_contents($this->docsRoot . '/manual/_indice_pantallas.json', json_encode([
            'pantallas' => [self::CTRL => ['titulo' => 'T', 'modulo' => 'actividades', 'manual' => [
                ['modulo' => 'actividades', 'titulo' => 'sin ancla'],
                ['modulo' => 'actividades', 'ancla' => 'ok', 'titulo' => 'Ok'],
            ]]],
        ], JSON_THROW_ON_ERROR));

        $r = $this->useCase()->execute(['controller' => self::CTRL]);

        self::assertSame(['ok'], array_column($r['tareas'], 'ancla'));
    }

    public function test_indice_real_del_repo_tiene_entrada_para_actividad_ver(): void
    {
        $real = new AyudaPantallaData(dirname(__DIR__, 4) . '/docs');

        $r = $real->execute(['controller' => self::CTRL]);

        self::assertTrue($r['encontrada']);
        self::assertSame('crear-una-actividad', $r['tareas'][0]['ancla']);
    }
}
