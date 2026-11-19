<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain\entity;

use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\AyudaConsultaId;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\myTest;

final class AyudaConsultaTest extends myTest
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440003';

    private AyudaConsulta $consulta;

    public function setUp(): void
    {
        parent::setUp();
        $this->consulta = new AyudaConsulta();
        $this->consulta->setUuid_item(self::UUID);
    }

    public function test_set_and_get_uuid_item(): void
    {
        $this->assertSame(self::UUID, $this->consulta->getUuid_item()->value());
        $this->consulta->setUuid_item(new AyudaConsultaId('550e8400-e29b-41d4-a716-446655440004'));
        $this->assertSame('550e8400-e29b-41d4-a716-446655440004', $this->consulta->getUuid_item()->value());
    }

    public function test_set_and_get_f_consulta(): void
    {
        $this->assertNull($this->consulta->getF_consulta());
        $fecha = new DateTimeLocal('2026-11-19 10:30:00');
        $this->consulta->setF_consulta($fecha);
        $this->assertSame($fecha, $this->consulta->getF_consulta());
    }

    public function test_set_and_get_pregunta(): void
    {
        $this->consulta->setPregunta('¿cómo apunto a alguien?');
        $this->assertSame('¿cómo apunto a alguien?', $this->consulta->getPregunta());
    }

    public function test_set_and_get_esquema(): void
    {
        $this->consulta->setEsquema('H-dlbv');
        $this->assertSame('H-dlbv', $this->consulta->getEsquema());
    }

    public function test_set_and_get_pantalla(): void
    {
        $this->consulta->setPantalla('frontend/notas/controller/acta_ver.php');
        $this->assertSame('frontend/notas/controller/acta_ver.php', $this->consulta->getPantalla());
    }

    public function test_set_and_get_n_resultados(): void
    {
        $this->assertSame(0, $this->consulta->getN_resultados());
        $this->consulta->setN_resultados('4');
        $this->assertSame(4, $this->consulta->getN_resultados());
    }

    public function test_set_and_get_puntuacion(): void
    {
        $this->assertNull($this->consulta->getPuntuacion());
        $this->consulta->setPuntuacion('12.50');
        $this->assertSame(12.5, $this->consulta->getPuntuacion());
        $this->consulta->setPuntuacion(null);
        $this->assertNull($this->consulta->getPuntuacion());
    }

    public function test_set_and_get_fuente(): void
    {
        $this->consulta->setFuente('guias/actividades/gestionar_asistentes.md');
        $this->assertSame('guias/actividades/gestionar_asistentes.md', $this->consulta->getFuente());
    }

    public function test_set_and_get_titulo(): void
    {
        $this->consulta->setTitulo('Apuntar a una persona');
        $this->assertSame('Apuntar a una persona', $this->consulta->getTitulo());
    }

    public function test_set_and_get_util(): void
    {
        $this->assertNull($this->consulta->getUtil());
        $this->consulta->setUtil(false);
        $this->assertFalse($this->consulta->getUtil());
        $this->consulta->setUtil(true);
        $this->assertTrue($this->consulta->getUtil());
    }

    public function test_set_and_get_comentario(): void
    {
        $this->consulta->setComentario('buscaba otra cosa');
        $this->assertSame('buscaba otra cosa', $this->consulta->getComentario());
    }

    public function test_set_and_get_estado(): void
    {
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $this->consulta->getEstado());
        $this->consulta->setEstado('2');
        $this->assertSame(AyudaConsultaEstado::RESUELTA, $this->consulta->getEstado());
        $this->consulta->setEstado(new AyudaConsultaEstado(AyudaConsultaEstado::DESCARTADA));
        $this->assertSame(AyudaConsultaEstado::DESCARTADA, $this->consulta->getEstadoVo()->value());
    }

    public function test_un_estado_que_no_existe_no_se_admite(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->consulta->setEstado(7);
    }

    public function test_para_la_bd_no_valorada_va_como_null_y_no_como_false(): void
    {
        $this->consulta->setPregunta('algo');
        $datos = $this->consulta->toArrayForDatabase();
        $this->assertNull($datos['util']);
        $this->assertSame(0, $datos['n_resultados']);
        $this->assertSame(1, $datos['estado']);

        $this->consulta->setUtil(false);
        $this->assertSame('false', $this->consulta->toArrayForDatabase()['util']);
    }
}
