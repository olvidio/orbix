<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaConsultaRegistrar;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\DateTimeLocal;

final class AyudaConsultaRegistrarTest extends TestCase
{
    /** @var list<AyudaConsulta> */
    private array $guardadas = [];

    /** @var list<DateTimeLocal> */
    private array $purgas = [];

    private function repositorio(bool $guardaBien = true): AyudaConsultaRepositoryInterface
    {
        $repo = $this->createMock(AyudaConsultaRepositoryInterface::class);
        $repo->method('Guardar')->willReturnCallback(function (AyudaConsulta $c) use ($guardaBien): bool {
            $this->guardadas[] = $c;

            return $guardaBien;
        });
        $repo->method('purgarAnterioresA')->willReturnCallback(function (DateTimeLocal $f): int {
            $this->purgas[] = $f;

            return 0;
        });

        return $repo;
    }

    /**
     * @return array<string, mixed>
     */
    private function resultado(string $pregunta, int $hits = 2): array
    {
        $todos = [
            ['titulo' => 'Apuntar a una persona', 'tipo' => 'guia', 'modulo' => 'actividades',
                'fuente' => 'docs/guias/actividades/gestionar_asistentes.md', 'score' => 18.75],
            ['titulo' => 'Otra cosa', 'tipo' => 'manual', 'modulo' => 'notas',
                'fuente' => 'docs/manual/notas.md', 'score' => 3.0],
        ];

        return [
            'pregunta' => $pregunta,
            'respuesta' => '…',
            'modo' => 'busqueda_local',
            'resultados' => array_slice($todos, 0, $hits),
        ];
    }

    public function test_registra_la_pregunta_con_su_primer_resultado(): void
    {
        $ahora = new DateTimeLocal('2026-11-19 10:30:00');
        $uuid = (new AyudaConsultaRegistrar($this->repositorio()))->execute(
            $this->resultado('¿cómo apunto a alguien?'),
            'H-dlbv',
            'http://orbix.local/orbix/frontend/asistentes/controller/form_asistentes.php?id=3',
            $ahora
        );

        $this->assertCount(1, $this->guardadas);
        $c = $this->guardadas[0];
        $this->assertSame($uuid, $c->getUuid_item()->value());
        $this->assertSame('¿cómo apunto a alguien?', $c->getPregunta());
        $this->assertSame('H-dlbv', $c->getEsquema());
        $this->assertSame('frontend/asistentes/controller/form_asistentes.php', $c->getPantalla());
        $this->assertSame(2, $c->getN_resultados());
        $this->assertSame(18.75, $c->getPuntuacion());
        $this->assertSame('docs/guias/actividades/gestionar_asistentes.md', $c->getFuente());
        $this->assertSame('Apuntar a una persona', $c->getTitulo());
        $this->assertSame('2026-11-19 10:30:00', $c->getF_consulta()?->format('Y-m-d H:i:s'));
        $this->assertNull($c->getUtil());
        $this->assertNull($c->getComentario());
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $c->getEstado());
    }

    public function test_una_pregunta_sin_resultados_tambien_se_registra(): void
    {
        $uuid = (new AyudaConsultaRegistrar($this->repositorio()))->execute($this->resultado('xyzzy', 0), 'H-dlbv', '');

        $this->assertNotSame('', $uuid);
        $c = $this->guardadas[0];
        $this->assertSame(0, $c->getN_resultados());
        $this->assertNull($c->getPuntuacion());
        $this->assertNull($c->getFuente());
        $this->assertNull($c->getTitulo());
        $this->assertNull($c->getPantalla());
    }

    public function test_sin_pregunta_no_se_registra_nada(): void
    {
        $uuid = (new AyudaConsultaRegistrar($this->repositorio()))->execute($this->resultado('   ', 0), 'H-dlbv', '');

        $this->assertSame('', $uuid);
        $this->assertSame([], $this->guardadas);
        $this->assertSame([], $this->purgas);
    }

    public function test_la_pantalla_solo_se_guarda_si_es_un_controlador_de_frontend(): void
    {
        $registrar = new AyudaConsultaRegistrar($this->repositorio());
        $registrar->execute($this->resultado('a'), 'H-dlbv', 'javascript:alert(1)');
        $registrar->execute($this->resultado('b'), 'H-dlbv', 'https://otra.web/algo.php');

        $this->assertNull($this->guardadas[0]->getPantalla());
        $this->assertNull($this->guardadas[1]->getPantalla());
    }

    public function test_una_pregunta_muy_larga_se_recorta(): void
    {
        (new AyudaConsultaRegistrar($this->repositorio()))->execute($this->resultado(str_repeat('ñ', 900)), 'H-dlbv', '');

        $this->assertSame(400, mb_strlen($this->guardadas[0]->getPregunta()));
    }

    public function test_sin_delegacion_en_la_sesion_el_esquema_queda_vacio(): void
    {
        (new AyudaConsultaRegistrar($this->repositorio()))->execute($this->resultado('a'), '', '');

        $this->assertNull($this->guardadas[0]->getEsquema());
    }

    public function test_si_no_se_puede_guardar_devuelve_vacio(): void
    {
        $uuid = (new AyudaConsultaRegistrar($this->repositorio(false)))->execute($this->resultado('a'), 'H-dlbv', '');

        $this->assertSame('', $uuid);
    }

    public function test_un_fallo_del_registro_no_rompe_la_ayuda(): void
    {
        $repo = $this->createMock(AyudaConsultaRepositoryInterface::class);
        $repo->method('Guardar')->willThrowException(new \RuntimeException('sin tabla'));

        $this->assertSame('', (new AyudaConsultaRegistrar($repo))->execute($this->resultado('a'), 'H-dlbv', ''));
    }

    public function test_al_registrar_se_purgan_las_de_mas_de_seis_meses(): void
    {
        (new AyudaConsultaRegistrar($this->repositorio()))->execute(
            $this->resultado('a'),
            'H-dlbv',
            '',
            new DateTimeLocal('2026-11-19 10:30:00')
        );

        $this->assertCount(1, $this->purgas);
        $this->assertSame('2026-05-19 10:30:00', $this->purgas[0]->format('Y-m-d H:i:s'));
    }
}
