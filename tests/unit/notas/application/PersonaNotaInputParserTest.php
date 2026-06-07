<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\support\PersonaNotaInputParser;
use src\notas\domain\value_objects\NotaEpoca;
use src\notas\domain\value_objects\TipoActa;

final class PersonaNotaInputParserTest extends TestCase
{
    public function test_eliminar_no_pide_asignatura_repository(): void
    {
        $parser = new PersonaNotaInputParser($this->createMock(AsignaturaRepositoryInterface::class));

        $pn = $parser->parse(
            [
                'id_pau' => 9,
                'id_asignatura' => 1002,
                'id_nivel' => 2100,
                'tipo_acta' => TipoActa::FORMATO_CERTIFICADO,
            ],
            eliminar: true
        );

        $this->assertSame(9, $pn->getId_nom());
        $this->assertSame(1002, $pn->getId_asignatura());
        $this->assertSame(2100, $pn->getId_nivel());
        $this->assertSame(TipoActa::FORMATO_CERTIFICADO, $pn->getTipo_acta());
    }

    public function test_id_asignatura_1_sin_filas_lanza(): void
    {
        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('getAsignaturas')->with(['id_nivel' => 3100])->willReturn([]);

        $parser = new PersonaNotaInputParser($repo);

        $this->expectException(\RuntimeException::class);
        $parser->parse([
            'id_pau' => 1,
            'id_asignatura' => 1,
            'id_nivel' => 3100,
            'tipo_acta' => 1,
        ], eliminar: true);
    }

    public function test_id_asignatura_1_toma_primera_asignatura(): void
    {
        $asig = $this->createMock(\src\asignaturas\domain\entity\Asignatura::class);
        $asig->method('getId_asignatura')->willReturn(3001);

        $repo = $this->createMock(AsignaturaRepositoryInterface::class);
        $repo->method('getAsignaturas')->willReturn([$asig]);

        $parser = new PersonaNotaInputParser($repo);

        $pn = $parser->parse([
            'id_pau' => 3,
            'id_asignatura' => 1,
            'id_nivel' => 2100,
            'tipo_acta' => 2,
        ], eliminar: true);

        $this->assertSame(3001, $pn->getId_asignatura());
    }

    public function test_tipo_acta_cero_se_normaliza_a_formato_acta(): void
    {
        $parser = new PersonaNotaInputParser($this->createMock(AsignaturaRepositoryInterface::class));

        $pn = $parser->parse([
            'id_pau' => 1,
            'id_asignatura' => 1002,
            'id_nivel' => 2100,
            'tipo_acta' => 0,
            'id_situacion' => 1,
            'epoca' => 0,
        ]);

        $this->assertSame(TipoActa::FORMATO_ACTA, $pn->getTipo_acta());
        $this->assertSame(NotaEpoca::EPOCA_OTRO, $pn->getEpocaVo()?->value());
    }
}
