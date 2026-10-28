<?php

namespace Tests\unit\shared\frontend;

use frontend\shared\PostRequest;
use Tests\myTest;

/**
 * Los errores de negocio que vuelven de /src/... llevan un bloque técnico (endpoint, página,
 * procedencia) que los controladores frontend deben recortar antes de enseñarlos al usuario.
 */
class PostRequestStripProvenanceTest extends myTest
{
    public function test_recorta_el_bloque_tecnico_tras_el_marcador(): void
    {
        $msg = 'los datos de asistencia los modifica el propietario de la plaza: dlmE';
        $conBloque = $msg . '<!-- postRequestDiagnostic --><br><strong>Endpoint:</strong> <code>http://x/src/a</code>'
            . '<br><strong>Procedencia:</strong> <code>frontend\shared\PostRequest::getData</code>';
        $this->assertSame($msg, PostRequest::stripInternalCallProvenance($conBloque));
    }

    public function test_recorta_el_formato_antiguo_sin_marcador(): void
    {
        $conBloque = 'aviso<br><strong>' . _('Procedencia') . ':</strong> <code>x</code>';
        $this->assertSame('aviso', PostRequest::stripInternalCallProvenance($conBloque));
    }

    public function test_un_mensaje_sin_bloque_queda_igual(): void
    {
        $this->assertSame('sin bloque', PostRequest::stripInternalCallProvenance('sin bloque'));
    }
}
