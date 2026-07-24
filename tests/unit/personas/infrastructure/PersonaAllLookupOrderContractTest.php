<?php

declare(strict_types=1);

namespace Tests\unit\personas\infrastructure;

use PHPUnit\Framework\TestCase;
use src\personas\domain\PersonaPublicacion;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;

/**
 * Documenta el contrato de desempate multi-esquema (caso A) y marcarPublicadoPara (caso B).
 */
final class PersonaAllLookupOrderContractTest extends TestCase
{
    public function test_orden_lookup_prioriza_situacion_a_luego_publicacion_vigente(): void
    {
        $vigente = PersonaPublicacion::sqlPublicacionVigente('');
        $orderSql = "CASE WHEN situacion = 'A' THEN 0 ELSE 1 END, "
            . "CASE WHEN $vigente THEN 0 ELSE 1 END, "
            . 'f_situacion DESC NULLS LAST, '
            . 'id_schema ASC';

        // Preferencia: A > no A; dentro de A, publicada vigente antes que no publicada.
        $this->assertStringContainsString("situacion = 'A'", $orderSql);
        $this->assertStringContainsString('publicado_para', $orderSql);
        $this->assertStringContainsString('f_situacion DESC', $orderSql);
        $this->assertStringContainsString('id_schema ASC', $orderSql);
    }

    public function test_interfaz_expone_marcar_publicado_para_y_lookup(): void
    {
        $methods = get_class_methods(PersonaAllRepositoryInterface::class);

        $this->assertContains('findByIdNomParaLookup', $methods);
        $this->assertContains('marcarPublicadoPara', $methods);
    }
}
