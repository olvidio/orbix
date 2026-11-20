<?php

namespace Tests\unit\tools\codegen;

use PHPUnit\Framework\TestCase;
use tools\codegen\DevelCodegenSupport;

require_once dirname(__DIR__, 4) . '/tools/codegen/DevelCodegenSupport.php';

/**
 * El generador de código escribe ficheros PHP en el repositorio: es un script de línea de órdenes, no una
 * pantalla ni un endpoint de la aplicación, y lo que recibe solo puede ser un identificador.
 */
class GenerarClasesEntradaTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../../../..';

    public function test_grupo_y_clase_solo_pueden_ser_identificadores(): void
    {
        $this->assertTrue(DevelCodegenSupport::esIdentificador('actividades'));
        $this->assertTrue(DevelCodegenSupport::esIdentificador('TipoDeActividad_2'));
        foreach (['', '../frontend/zz', 'a/b', 'a b', '9a', "a\nb", 'a;b', 'a{', 'a.b'] as $malo) {
            $this->assertFalse(DevelCodegenSupport::esIdentificador($malo), $malo);
        }
    }

    public function test_el_texto_del_comentario_no_puede_cerrarlo(): void
    {
        $this->assertTrue(DevelCodegenSupport::esTextoDeComentario('delegación'));
        $this->assertTrue(DevelCodegenSupport::esTextoDeComentario('orbix sv-e'));
        $this->assertFalse(DevelCodegenSupport::esTextoDeComentario('x */ phpinfo(); /*'));
        $this->assertFalse(DevelCodegenSupport::esTextoDeComentario("x\ny"));
    }

    public function test_el_generador_no_esta_en_la_web(): void
    {
        $this->assertDirectoryDoesNotExist(self::RAIZ . '/frontend/devel_codegen');
        $this->assertDirectoryDoesNotExist(self::RAIZ . '/src/devel_codegen');
        $this->assertFileExists(self::RAIZ . '/tools/codegen/generar_clases.php');
    }

    public function test_el_script_solo_corre_por_linea_de_ordenes(): void
    {
        $codigo = (string) file_get_contents(self::RAIZ . '/tools/codegen/generar_clases.php');
        $guarda = strpos($codigo, "if (PHP_SAPI !== 'cli') {");
        $this->assertNotFalse($guarda);
        // Antes de la guarda no se carga ni se ejecuta nada.
        $antes = substr($codigo, 0, (int) $guarda);
        foreach (['require', 'include', 'mkdir(', 'file_put_contents(', 'fopen(', 'getopt('] as $accion) {
            $this->assertStringNotContainsString($accion, $antes, $accion);
        }
    }

    public function test_valida_los_nombres_antes_de_escribir(): void
    {
        $codigo = (string) file_get_contents(self::RAIZ . '/tools/codegen/generar_clases.php');
        $validacion = strpos($codigo, 'DevelCodegenSupport::esIdentificador(');
        $this->assertNotFalse($validacion);
        $antes = substr($codigo, 0, (int) $validacion);
        foreach (['mkdir(', 'rename(', 'file_put_contents(', 'fopen(', 'fwrite($handle', 'unlink('] as $escritura) {
            $this->assertStringNotContainsString($escritura, $antes, $escritura);
        }
    }
}
