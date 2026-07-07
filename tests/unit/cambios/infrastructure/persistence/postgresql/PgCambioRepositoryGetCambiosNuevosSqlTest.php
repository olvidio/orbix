<?php

declare(strict_types=1);

namespace Tests\unit\cambios\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;

/**
 * Regresión: getCambiosNuevos() debe seleccionar id_status para la comparación de fases.
 */
final class PgCambioRepositoryGetCambiosNuevosSqlTest extends TestCase
{
    public function test_get_cambios_nuevos_incluye_id_status_en_ambas_consultas(): void
    {
        $source = file_get_contents(
            __DIR__ . '/../../../../../../src/cambios/infrastructure/persistence/postgresql/PgCambioRepository.php'
        );
        $this->assertIsString($source);

        preg_match_all('/SELECT c\.id_schema.*?FROM/is', $source, $matches);
        $this->assertGreaterThanOrEqual(2, count($matches[0]));

        foreach ($matches[0] as $select) {
            $this->assertStringContainsString('c.id_status', $select, 'Cada SELECT de getCambiosNuevos debe incluir id_status');
        }
    }
}
