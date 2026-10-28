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

    /**
     * Los controladores que muestran errores de negocio de sus datos deben usar exitOnError=false
     * y recortar el bloque; con el valor por defecto `exit($error)` lo enseña entero.
     */
    public function test_los_formularios_con_regla_de_propietario_recortan_el_bloque_tecnico(): void
    {
        foreach (['form_asistentes_a_una_actividad', 'form_actividades_de_una_persona'] as $c) {
            $src = (string) file_get_contents(dirname(__DIR__, 4) . "/frontend/asistentes/controller/$c.php");
            $this->assertStringContainsString('stripInternalCallProvenance', $src, $c);
            $this->assertMatchesRegularExpression('/getDataFromUrl\([^;]*\$campos,\s*false\)/', $src, $c);
        }
    }
}
