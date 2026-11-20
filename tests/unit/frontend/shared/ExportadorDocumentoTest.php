<?php

declare(strict_types=1);

namespace Tests\unit\frontend\shared;

use frontend\shared\helpers\ExportadorDocumento;
use PHPUnit\Framework\TestCase;

/**
 * Exportación de un bloque de la pantalla a fichero (html, zip, hoja de cálculo, texto). El título que
 * llega de la petición solo sirve para el nombre de la descarga: no forma parte de ninguna ruta ni orden.
 */
final class ExportadorDocumentoTest extends TestCase
{
    private const TABLA = '<div><table><tr><th>a</th></tr><tr><td>uno &amp; dos</td></tr></table></div>';

    public function test_el_nombre_de_la_descarga_solo_lleva_caracteres_de_nombre_de_fichero(): void
    {
        $this->assertSame('lista_de_ca.html', ExportadorDocumento::nombreDeDescarga('lista de ca', 'html'));
        $this->assertSame('a_id_false.ods', ExportadorDocumento::nombreDeDescarga('a;id;false', 'ods'));
        $this->assertSame('etc_passwd.zip', ExportadorDocumento::nombreDeDescarga('../../etc/passwd', 'zip'));
        $this->assertSame('export.odt', ExportadorDocumento::nombreDeDescarga('$(`"\')', 'odt'));
        $this->assertSame('Asistència_2026.html', ExportadorDocumento::nombreDeDescarga("Asistència\r\n2026", 'html'));
    }

    public function test_html_quita_las_imagenes_y_no_toca_el_disco(): void
    {
        $r = (new ExportadorDocumento())->generar('html', 't', '<p>hola<img src="x.png"></p>', '');

        $this->assertSame('t.html', $r['nombre']);
        $this->assertSame('<meta charset="utf-8"><p>hola</p>', $r['contenido']);
    }

    public function test_el_zip_lleva_el_html_y_un_titulo_con_orden_no_se_ejecuta(): void
    {
        $marca = sys_get_temp_dir() . '/zz_export_marca_' . getmypid();
        @unlink($marca);

        $r = (new ExportadorDocumento())->generar('html_zip', '$(touch ' . $marca . ')";touch ' . $marca . ';"', self::TABLA, '');

        $this->assertFileDoesNotExist($marca);
        $this->assertStringEndsWith('.zip', $r['nombre']);
        $this->assertSame('PK', substr($r['contenido'], 0, 2));
    }

    public function test_la_hoja_de_calculo_y_el_texto_son_documentos_odf_y_no_ejecutan_el_titulo(): void
    {
        if (trim((string) shell_exec('command -v xsltproc')) === '') {
            $this->markTestSkipped('xsltproc no está instalado.');
        }
        $marca = sys_get_temp_dir() . '/zz_export_marca2_' . getmypid();
        @unlink($marca);
        $antes = glob(sys_get_temp_dir() . '/orbix_export_*') ?: [];

        foreach (['odfc' => 'ods', 'odft' => 'odt'] as $tipo => $ext) {
            $r = (new ExportadorDocumento())->generar($tipo, 'a;touch ' . $marca . ';false', self::TABLA, 'frontend/encargossacd/view/listas_com_ctr.phtml');

            $this->assertFileDoesNotExist($marca);
            $this->assertSame(ExportadorDocumento::nombreDeDescarga('a;touch ' . $marca . ';false', $ext), $r['nombre']);
            $this->assertDoesNotMatchRegularExpression('/[^\\p{L}\\p{N}_.-]/u', $r['nombre']);
            $this->assertSame('PK', substr($r['contenido'], 0, 2));
            $this->assertStringContainsString('content.xml', $r['contenido']);
        }
        $this->assertSame($antes, glob(sys_get_temp_dir() . '/orbix_export_*') ?: [], 'no quedan ficheros temporales');
    }

    public function test_un_tipo_desconocido_se_rechaza(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ExportadorDocumento())->generar('php', 't', 'x', '');
    }
}
