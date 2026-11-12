<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;
use Tests\myTest;

/**
 * Autocompletado de examinadores (`/src/notas/examinadores_search`): el texto del usuario filtra por nombre.
 */
final class PgActaTribunalExaminadoresTest extends myTest
{
    private function buscar(string $texto): mixed
    {
        $json = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class)->getJsonExaminadores($texto);
        $datos = json_decode($json, true);
        $this->assertIsArray($datos, "la respuesta debe ser JSON válido: $json");

        return $datos;
    }

    public function test_devuelve_hasta_25_examinadores_como_label(): void
    {
        $todos = $this->buscar('');

        $this->assertLessThanOrEqual(25, count($todos));
        foreach ($todos as $fila) {
            $this->assertSame(['label'], array_keys($fila));
        }
    }

    /**
     * Regresión: el texto se interpolaba en el SQL; «x' OR '1'='1» devolvía examinadores (y un UNION permitía leer
     * cualquier tabla).
     */
    public function test_el_texto_no_se_interpola_en_el_sql(): void
    {
        $this->assertSame([], $this->buscar("x' OR '1'='1"));
        $this->assertSame([], $this->buscar("zzzz'); SELECT 1; --"));
    }

    public function test_una_comilla_en_la_busqueda_no_rompe_la_consulta_ni_el_json(): void
    {
        $this->assertIsArray($this->buscar("O'Brien"));
    }
}
