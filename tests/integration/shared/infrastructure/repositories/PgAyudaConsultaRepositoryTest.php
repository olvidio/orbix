<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure\repositories;

use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\criteria\AyudaConsultaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\factories\shared\AyudaConsultaFactory;
use Tests\myTest;

class PgAyudaConsultaRepositoryTest extends myTest
{
    private AyudaConsultaRepositoryInterface $repository;
    private AyudaConsultaFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AyudaConsultaRepositoryInterface::class);
        $this->factory = new AyudaConsultaFactory();
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $propias = $this->repository->matching(
            Criteria::create()->where(Filter::eq(F::Esquema, AyudaConsultaFactory::ESQUEMA))
        );
        foreach ($propias as $consulta) {
            $this->repository->Eliminar($consulta);
        }
    }

    public function test_guardar_nuevo(): void
    {
        $consulta = $this->factory->createSimple();
        $uuid = $consulta->getUuid_item()->value();
        $this->assertNull($this->repository->findById($uuid));

        $this->assertTrue($this->repository->Guardar($consulta));

        $leida = $this->repository->findById($uuid);
        $this->assertInstanceOf(AyudaConsulta::class, $leida);
        $this->assertSame($uuid, $leida->getUuid_item()->value());
        $this->assertSame('¿cómo apunto a una persona a una actividad?', $leida->getPregunta());
        $this->assertSame(AyudaConsultaFactory::ESQUEMA, $leida->getEsquema());
        $this->assertSame('frontend/asistentes/controller/form_asistentes.php', $leida->getPantalla());
        $this->assertSame(3, $leida->getN_resultados());
        $this->assertSame(12.5, $leida->getPuntuacion());
        $this->assertSame('guias/actividades/gestionar_asistentes.md', $leida->getFuente());
        $this->assertSame('Apuntar a una persona', $leida->getTitulo());
        $this->assertNull($leida->getUtil());
        $this->assertNull($leida->getComentario());
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $leida->getEstado());
    }

    public function test_la_fecha_se_guarda_y_se_lee_con_su_hora(): void
    {
        $consulta = $this->factory->createSimple();
        $consulta->setF_consulta(new DateTimeLocal('2026-03-04 05:06:07'));
        $this->repository->Guardar($consulta);

        $leida = $this->repository->findById($consulta->getUuid_item()->value());
        $this->assertInstanceOf(DateTimeLocal::class, $leida?->getF_consulta());
        $this->assertSame('2026-03-04 05:06:07', $leida->getF_consulta()->format('Y-m-d H:i:s'));
    }

    public function test_una_pregunta_sin_resultados_se_guarda_con_cero(): void
    {
        $consulta = $this->factory->createSimple();
        $consulta->setN_resultados(0);
        $consulta->setPuntuacion(null);
        $consulta->setFuente(null);
        $consulta->setTitulo(null);
        $this->assertTrue($this->repository->Guardar($consulta));

        $leida = $this->repository->findById($consulta->getUuid_item()->value());
        $this->assertSame(0, $leida?->getN_resultados());
        $this->assertNull($leida->getPuntuacion());
        $this->assertNull($leida->getFuente());
    }

    public function test_actualizar_existente_y_no_toca_las_demas(): void
    {
        $una = $this->factory->createSimple();
        $otra = $this->factory->createSimple();
        $this->repository->Guardar($una);
        $this->repository->Guardar($otra);

        $una->setUtil(false);
        $una->setComentario('buscaba cómo borrarla');
        $una->setEstado(AyudaConsultaEstado::RESUELTA);
        $this->assertTrue($this->repository->Guardar($una));

        $leida = $this->repository->findById($una->getUuid_item()->value());
        $this->assertFalse($leida?->getUtil());
        $this->assertSame('buscaba cómo borrarla', $leida->getComentario());
        $this->assertSame(AyudaConsultaEstado::RESUELTA, $leida->getEstado());

        $intacta = $this->repository->findById($otra->getUuid_item()->value());
        $this->assertNull($intacta?->getUtil());
        $this->assertNull($intacta?->getComentario());
        $this->assertSame(AyudaConsultaEstado::PENDIENTE, $intacta?->getEstado());
    }

    public function test_la_valoracion_positiva_se_lee_como_true(): void
    {
        $consulta = $this->factory->createSimple();
        $consulta->setUtil(true);
        $this->repository->Guardar($consulta);

        $this->assertTrue($this->repository->findById($consulta->getUuid_item()->value())?->getUtil());
    }

    public function test_find_by_id_no_existente(): void
    {
        $this->assertNull($this->repository->findById('00000000-0000-0000-0000-000000000000'));
        $this->assertNull($this->repository->findById('no-es-un-uuid'));
    }

    public function test_eliminar_borra_solo_esa(): void
    {
        $una = $this->factory->createSimple();
        $otra = $this->factory->createSimple();
        $this->repository->Guardar($una);
        $this->repository->Guardar($otra);

        $this->assertTrue($this->repository->Eliminar($una));

        $this->assertNull($this->repository->findById($una->getUuid_item()->value()));
        $this->assertNotNull($this->repository->findById($otra->getUuid_item()->value()));
    }

    public function test_matching_filtra_y_ordena(): void
    {
        $antigua = $this->factory->createSimple();
        $antigua->setF_consulta(new DateTimeLocal('2026-01-01 10:00:00'));
        $reciente = $this->factory->createSimple();
        $reciente->setF_consulta(new DateTimeLocal('2026-02-01 10:00:00'));
        $reciente->setUtil(false);
        $resuelta = $this->factory->createSimple();
        $resuelta->setF_consulta(new DateTimeLocal('2026-03-01 10:00:00'));
        $resuelta->setEstado(AyudaConsultaEstado::RESUELTA);
        foreach ([$antigua, $reciente, $resuelta] as $consulta) {
            $this->repository->Guardar($consulta);
        }

        $pendientes = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(F::Esquema, AyudaConsultaFactory::ESQUEMA))
                ->andWhere(Filter::eq(F::Estado, AyudaConsultaEstado::PENDIENTE))
                ->orderBy(F::FConsulta, \src\shared\domain\criteria\OrderDirection::Desc)
        );
        $this->assertSame(
            [$reciente->getUuid_item()->value(), $antigua->getUuid_item()->value()],
            array_map(static fn (AyudaConsulta $c): string => $c->getUuid_item()->value(), $pendientes)
        );

        $noSirvieron = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(F::Esquema, AyudaConsultaFactory::ESQUEMA))
                ->andWhere(Filter::eq(F::Util, false))
        );
        $this->assertCount(1, $noSirvieron);
        $this->assertSame($reciente->getUuid_item()->value(), $noSirvieron[0]->getUuid_item()->value());
    }

    /**
     * La purga se prueba con una fecha de corte muy antigua (año 1990) para que no pueda
     * alcanzar ninguna consulta que no sea de este test.
     */
    public function test_purgar_borra_las_antiguas_y_conserva_las_marcadas_sin_revisar(): void
    {
        $corte = new DateTimeLocal('1990-06-01 00:00:00');

        $vieja = $this->factory->createSimple();
        $vieja->setF_consulta(new DateTimeLocal('1990-01-01 10:00:00'));
        $viejaQueSirvio = $this->factory->createSimple();
        $viejaQueSirvio->setF_consulta(new DateTimeLocal('1990-01-02 10:00:00'));
        $viejaQueSirvio->setUtil(true);
        $viejaSinRevisar = $this->factory->createSimple();
        $viejaSinRevisar->setF_consulta(new DateTimeLocal('1990-01-03 10:00:00'));
        $viejaSinRevisar->setUtil(false);
        $viejaYaRevisada = $this->factory->createSimple();
        $viejaYaRevisada->setF_consulta(new DateTimeLocal('1990-01-04 10:00:00'));
        $viejaYaRevisada->setUtil(false);
        $viejaYaRevisada->setEstado(AyudaConsultaEstado::RESUELTA);
        $posterior = $this->factory->createSimple();
        $posterior->setF_consulta(new DateTimeLocal('1990-07-01 10:00:00'));
        foreach ([$vieja, $viejaQueSirvio, $viejaSinRevisar, $viejaYaRevisada, $posterior] as $consulta) {
            $this->repository->Guardar($consulta);
        }

        $this->assertSame(3, $this->repository->purgarAnterioresA($corte));

        $this->assertNull($this->repository->findById($vieja->getUuid_item()->value()));
        $this->assertNull($this->repository->findById($viejaQueSirvio->getUuid_item()->value()));
        $this->assertNull($this->repository->findById($viejaYaRevisada->getUuid_item()->value()));
        $this->assertNotNull($this->repository->findById($viejaSinRevisar->getUuid_item()->value()));
        $this->assertNotNull($this->repository->findById($posterior->getUuid_item()->value()));
    }
}
