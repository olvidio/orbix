<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use src\shared\application\AyudaConsultasListaData;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use Tests\myTest;
use Tests\support\DescribeCriteria;

final class AyudaConsultasListaDataTest extends myTest
{
    private ?Criteria $criterio = null;

    /**
     * @param list<AyudaConsulta> $filas
     */
    private function repositorio(array $filas): AyudaConsultaRepositoryInterface
    {
        $repo = $this->createMock(AyudaConsultaRepositoryInterface::class);
        $repo->method('matching')->willReturnCallback(function (Criteria $c) use ($filas): array {
            $this->criterio = $c;

            return $filas;
        });

        return $repo;
    }

    private function consulta(
        int $n,
        string $pregunta,
        string $fecha,
        int $resultados = 2,
        ?bool $util = null,
        ?string $comentario = null,
        string $esquema = 'H-dlbv',
    ): AyudaConsulta {
        $c = new AyudaConsulta();
        $c->setUuid_item(sprintf('550e8400-e29b-41d4-a716-4466554400%02d', $n));
        $c->setF_consulta(new DateTimeLocal($fecha));
        $c->setPregunta($pregunta);
        $c->setEsquema($esquema);
        $c->setPantalla('frontend/asistentes/controller/form_asistentes.php');
        $c->setN_resultados($resultados);
        if ($resultados > 0) {
            $c->setPuntuacion(12.5);
            $c->setFuente('docs/guias/actividades/gestionar_asistentes.md');
            $c->setTitulo('Apuntar a una persona');
        }
        $c->setUtil($util);
        $c->setComentario($comentario);

        return $c;
    }

    public function test_por_defecto_busca_las_pendientes_mas_recientes(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([])))->execute([]);

        $this->assertNotNull($this->criterio);
        $this->assertSame(['estado eq 1'], DescribeCriteria::filtros($this->criterio));
        $this->assertSame(['f_consulta desc'], DescribeCriteria::orden($this->criterio));
        $this->assertSame(2000, $this->criterio->getLimit());
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $data['estado']);
        $this->assertSame('problemas', $data['ver']);
        $this->assertSame([], $data['grupos']);
        $this->assertSame('', $data['tsv']);
        $this->assertSame([['1', 'pendiente'], ['2', 'resuelta'], ['3', 'descartada']], $data['opciones_estado']);
    }

    public function test_se_puede_pedir_otro_estado(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([])))->execute(['estado' => '2']);

        $this->assertNotNull($this->criterio);
        $this->assertSame(['estado eq 2'], DescribeCriteria::filtros($this->criterio));
        $this->assertSame(AyudaConsultaEstado::RESUELTA, $data['estado']);
    }

    public function test_un_estado_que_no_existe_se_toma_como_pendiente(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([])))->execute(['estado' => '77']);

        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $data['estado']);
    }

    public function test_agrupa_la_misma_pregunta_aunque_cambien_mayusculas_acentos_y_signos(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([
            $this->consulta(1, '¿Cómo apunto a alguien?', '2026-11-19 10:00:00', 0),
            $this->consulta(2, 'como apunto a  alguien', '2026-11-18 09:00:00', 0, null, null, 'H-dlmEv'),
            $this->consulta(3, 'COMO APUNTO A ALGUIEN?', '2026-11-17 09:00:00', 0, false, 'no sale nada'),
        ])))->execute([]);

        $this->assertCount(1, $data['grupos']);
        $g = $data['grupos'][0];
        $this->assertSame('¿Cómo apunto a alguien?', $g['pregunta']);
        $this->assertSame(3, $g['veces']);
        $this->assertSame('2026-11-19 10:00', $g['ultima']);
        $this->assertSame(1, $g['no_sirvio']);
        $this->assertSame(0, $g['sirvio']);
        $this->assertSame(['no sale nada'], $g['comentarios']);
        $this->assertSame(['H-dlbv', 'H-dlmEv'], $g['esquemas']);
        $this->assertSame(['frontend/asistentes/controller/form_asistentes.php'], $g['pantallas']);
        $this->assertSame(0, $g['n_resultados']);
        $this->assertSame('', $g['fuente']);
        $this->assertSame('', $g['titulo']);
    }

    public function test_el_grupo_lleva_el_primer_resultado_de_la_consulta_mas_reciente(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([
            $this->consulta(1, 'como borro un acta', '2026-11-19 10:00:00', 4, false),
        ])))->execute([]);

        $g = $data['grupos'][0];
        $this->assertSame(4, $g['n_resultados']);
        $this->assertSame(12.5, $g['puntuacion']);
        $this->assertSame('docs/guias/actividades/gestionar_asistentes.md', $g['fuente']);
        $this->assertSame('Apuntar a una persona', $g['titulo']);
    }

    public function test_por_defecto_solo_salen_las_que_no_sirvieron_o_no_tuvieron_respuesta(): void
    {
        $filas = [
            $this->consulta(1, 'respondida y sin valorar', '2026-11-19 10:00:00', 3),
            $this->consulta(2, 'respondida y sirvio', '2026-11-19 09:00:00', 3, true),
            $this->consulta(3, 'sin respuesta', '2026-11-19 08:00:00', 0),
            $this->consulta(4, 'no sirvio', '2026-11-19 07:00:00', 3, false),
        ];

        $problemas = (new AyudaConsultasListaData($this->repositorio($filas)))->execute([]);
        $this->assertSame(
            ['no sirvio', 'sin respuesta'],
            array_column($problemas['grupos'], 'pregunta')
        );
        $this->assertSame(4, $problemas['total_consultas']);

        $todas = (new AyudaConsultasListaData($this->repositorio($filas)))->execute(['ver' => 'todas']);
        $this->assertSame('todas', $todas['ver']);
        $this->assertSame(
            ['no sirvio', 'sin respuesta', 'respondida y sin valorar', 'respondida y sirvio'],
            array_column($todas['grupos'], 'pregunta')
        );
    }

    public function test_orden_primero_las_marcadas_luego_sin_respuesta_luego_las_mas_repetidas(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([
            $this->consulta(1, 'sin respuesta una vez', '2026-11-19 10:00:00', 0),
            $this->consulta(2, 'sin respuesta dos veces', '2026-11-18 10:00:00', 0),
            $this->consulta(3, 'sin respuesta dos veces', '2026-11-17 10:00:00', 0),
            $this->consulta(4, 'marcada', '2026-11-01 10:00:00', 5, false),
        ])))->execute([]);

        $this->assertSame(
            ['marcada', 'sin respuesta dos veces', 'sin respuesta una vez'],
            array_column($data['grupos'], 'pregunta')
        );
    }

    public function test_cada_grupo_firma_sus_acciones_con_las_consultas_que_agrupa(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([
            $this->consulta(1, 'sin respuesta', '2026-11-19 10:00:00', 0),
            $this->consulta(2, 'sin respuesta', '2026-11-18 10:00:00', 0),
        ])))->execute([]);

        $g = $data['grupos'][0];
        $uuids = ['550e8400-e29b-41d4-a716-446655440001', '550e8400-e29b-41d4-a716-446655440002'];
        $this->assertSame(
            ['uuids' => $uuids, 'estado' => AyudaConsultaEstado::RESUELTA],
            HashB::open($g['ctx_resolver'], 'ayuda_consultas_estado_update')
        );
        $this->assertSame(
            ['uuids' => $uuids, 'estado' => AyudaConsultaEstado::DESCARTADA],
            HashB::open($g['ctx_descartar'], 'ayuda_consultas_estado_update')
        );
        $this->assertSame('', $g['ctx_reabrir']);
    }

    public function test_las_que_no_estan_pendientes_solo_se_pueden_reabrir(): void
    {
        $resuelta = $this->consulta(1, 'sin respuesta', '2026-11-19 10:00:00', 0);
        $resuelta->setEstado(AyudaConsultaEstado::RESUELTA);

        $g = (new AyudaConsultasListaData($this->repositorio([$resuelta])))->execute(['estado' => 2])['grupos'][0];

        $this->assertSame('', $g['ctx_resolver']);
        $this->assertSame('', $g['ctx_descartar']);
        $this->assertSame(
            ['uuids' => ['550e8400-e29b-41d4-a716-446655440001'], 'estado' => AyudaConsultaEstado::PENDIENTE],
            HashB::open($g['ctx_reabrir'], 'ayuda_consultas_estado_update')
        );
    }

    public function test_el_tsv_lleva_la_pregunta_y_la_guia_que_salio_lista_para_ayuda_preguntas_tsv(): void
    {
        $data = (new AyudaConsultasListaData($this->repositorio([
            $this->consulta(1, "como borro\tun acta", '2026-11-19 10:00:00', 4, false),
            $this->consulta(2, 'xyzzy', '2026-11-18 10:00:00', 0),
        ])))->execute([]);

        $this->assertSame(
            "como borro un acta\tactividades/gestionar_asistentes\t\nxyzzy\t\t\n",
            $data['tsv']
        );
    }
}
