<?php

declare(strict_types=1);

namespace Tests\unit\notas\application\support;

use PHPUnit\Framework\TestCase;
use src\notas\application\support\PersonaNotaPkPorAsignatura;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\TipoActa;

final class PersonaNotaPkPorAsignaturaTest extends TestCase
{
    public function test_sin_filas_es_insert(): void
    {
        $this->assertNull(PersonaNotaPkPorAsignatura::pkParaUpdate([]));
    }

    public function test_usa_el_hueco_almacenado_no_el_del_catalogo(): void
    {
        $existente = new PersonaNota([
            'id_nom' => 1004199,
            'id_asignatura' => 2312,
            'id_nivel' => 2212,
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ]);

        $this->assertSame([
            'id_nivel' => 2212,
            'tipo_acta' => TipoActa::FORMATO_ACTA,
        ], PersonaNotaPkPorAsignatura::pkParaUpdate([$existente]));
    }
}
