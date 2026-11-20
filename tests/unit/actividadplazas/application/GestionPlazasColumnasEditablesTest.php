<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\GestionPlazasUpdate;

/**
 * Qué celda del cuadro de plazas se puede cambiar lo decide el servidor al pintar la fila y viaja en la
 * cápsula: el endpoint no acepta otra columna aunque el navegador la mande (revisión de seguridad del
 * 2026-11-20, JS-07).
 */
final class GestionPlazasColumnasEditablesTest extends TestCase
{
    public function test_las_columnas_editables_de_una_fila(): void
    {
        $fila = [
            'id' => 7,
            'actividad' => 'ca',
            'tot' => ['editable' => 'true', 'valor' => 30],
            'dlb-c' => ['editable' => 'false', 'valor' => 1],
            'dlb-p' => ['editable' => 'true', 'valor' => 2],
        ];

        $this->assertSame(['tot', 'dlb-p'], GestionPlazasUpdate::columnasEditables($fila));
    }

    public function test_solo_se_admite_una_columna_de_la_capsula(): void
    {
        $ctx = ['id_activ' => 7, 'editables' => ['tot', 'dlb-p']];

        $this->assertTrue(GestionPlazasUpdate::columnaAdmitida($ctx, 'dlb-p'));
        $this->assertFalse(GestionPlazasUpdate::columnaAdmitida($ctx, 'dlb-c'));
        $this->assertFalse(GestionPlazasUpdate::columnaAdmitida(['id_activ' => 7], 'tot'), 'una cápsula sin lista no admite ninguna');
    }

    public function test_el_endpoint_lo_comprueba(): void
    {
        $codigo = (string) file_get_contents(dirname(__DIR__, 4) . '/src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_update.php');

        $this->assertStringContainsString('GestionPlazasUpdate::columnaAdmitida(', $codigo);
    }
}
