<?php

declare(strict_types=1);

namespace Tests\unit\frontend;

use PHPUnit\Framework\TestCase;

/**
 * Las plantillas que montan el HTML de los PDF (mPDF) escapan los datos: nombres, notas, número de acta
 * o de certificado. mPDF interpreta ese HTML (etiquetas, imágenes remotas), así que un dato sin escapar
 * altera el documento o hace que el servidor pida una URL (revisión de seguridad del 2026-11-20, FIC-05).
 */
final class PlantillasMpdfEscapadasTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../../..';

    public function test_los_datos_no_se_imprimen_sin_escapar(): void
    {
        $sinEscapar = [
            'frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php' => [
                '<?= $certificado ?>', '<?= $vstgr ?>', '<?= $lugar_fecha ?>', "<?= \$row['nota_txt'] ?>", "<?= \$row['creditos'] ?>",
                "strtr(\$oAsignatura['nombre_asignatura'], \$replace)", "strtr(\$row['nombre_asignatura'], \$replace)",
            ],
            'frontend/notas/controller/acta_imprimir_mpdf.php' => [
                '<?= $nom; ?>', '<?= $nota; ?>', '<?= $acta; ?>', '<?= $libro; ?>', '<?= $pagina; ?>', '<?= $linea; ?>', '<?= $reg_stgr ?>',
            ],
            'frontend/notas/controller/tessera_imprimir_mpdf.php' => [
                'DOMINI: <?= $nom ?>', "strtr(\$fila['nombre'], \$replace)", "'' : \$fila['nota'] ?>", "'' : \$fila['fecha_local'] ?>", "'' : \$fila['acta'] ?>",
            ],
        ];
        foreach ($sinEscapar as $fichero => $trozos) {
            $codigo = (string) file_get_contents(self::RAIZ . '/' . $fichero);
            foreach ($trozos as $trozo) {
                $this->assertStringNotContainsString($trozo, $codigo, $fichero);
            }
        }
    }
}
