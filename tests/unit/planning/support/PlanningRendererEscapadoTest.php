<?php

declare(strict_types=1);

namespace Tests\unit\planning\support;

use PHPUnit\Framework\TestCase;

/**
 * El planning monta su tabla por concatenación con nombres de actividades y de personas: se escapan en
 * cuanto se leen del payload, antes de usarlos en celdas y atributos `title` (revisión de seguridad del
 * 2026-11-20, XSS-03).
 */
final class PlanningRendererEscapadoTest extends TestCase
{
    public function test_los_nombres_se_escapan_al_leerlos(): void
    {
        $codigo = (string) file_get_contents(__DIR__ . '/../../../../frontend/planning/support/PlanningRenderer.php');

        $this->assertStringNotContainsString("\$nom_curt[\$a] = \\frontend\\shared\\helpers\\PayloadCoercion::string(", $codigo);
        $this->assertStringNotContainsString("\$nom[\$a] = \\frontend\\shared\\helpers\\PayloadCoercion::string(", $codigo);
        $this->assertStringContainsString('$persona = self::e((string) $persona);', $codigo);
        $this->assertStringContainsString("\$nom_curt[\$a] = self::e(", $codigo);
    }
}
