<?php

declare(strict_types=1);

namespace Tests\unit\frontend\certificados;

use frontend\certificados\helpers\CertificadosPayload;
use PHPUnit\Framework\TestCase;

/**
 * Los mapeos de las respuestas de `/src/certificados/...` no pierden la cápsula `HashB` que la
 * pantalla tiene que reenviar (fase 6.5): si se pierde, la acción responde «Operación no autorizada».
 */
final class CertificadosPayloadCapsulasTest extends TestCase
{
    public function test_el_formulario_de_subir_el_pdf_firmado_conserva_la_capsula(): void
    {
        $form = CertificadosPayload::uploadFirmadoFromPayload(['id_item' => 7, 'id_nom' => 10011, 'nom' => 'x', 'apellidos_nombre' => 'y', 'ctx_guardar' => 'CAPSULA']);

        $this->assertSame('CAPSULA', $form['ctx_guardar'] ?? null);
        $this->assertSame([7, 10011], [$form['id_item'], $form['id_nom']]);
    }

    public function test_las_filas_de_la_lista_de_emitidos_conservan_sus_capsulas(): void
    {
        $tabla = CertificadosPayload::emitidoListaTablaFromPayload([
            'a_cabeceras' => [], 'a_botones' => [],
            'a_valores' => [1 => ['sel' => 7, 'ctx_eliminar' => 'BORRAR', 'ctx_enviar' => 'ENVIAR', 1 => 'H 1/26']],
        ]);

        $this->assertSame(['BORRAR', 'ENVIAR'], [$tabla['valores'][1]['ctx_eliminar'] ?? null, $tabla['valores'][1]['ctx_enviar'] ?? null]);
    }
}
