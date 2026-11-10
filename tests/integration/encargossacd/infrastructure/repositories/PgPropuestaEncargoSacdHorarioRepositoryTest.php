<?php

namespace Tests\integration\encargossacd\infrastructure\repositories;

use PDO;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdHorarioField;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\encargossacd\domain\value_objects\DiaRefCode;
use src\encargossacd\domain\value_objects\MasMenosCode;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\infrastructure\GlobalPdo;
use Tests\factories\encargossacd\EncargoSacdHorarioFactory;
use Tests\factories\encargossacd\PropuestaEncargoSacdFactory;
use Tests\myTest;

/**
 * Staging `propuesta_encargo_sacd_horario` (FK id_item_tarea_sacd → propuesta_encargos_sacd
 * con ON DELETE CASCADE). Si la propuesta no se ha creado en la base de pruebas, se omiten.
 */
class PgPropuestaEncargoSacdHorarioRepositoryTest extends myTest
{
    private PropuestaEncargoSacdHorarioRepositoryInterface $repository;
    private PropuestaEncargoSacdRepositoryInterface $repoPropuesta;
    private EncargoSacdHorarioFactory $factory;
    private PDO $pdo;

    /** @var list<int> */
    private array $idsHorario = [];
    /** @var list<PropuestaEncargoSacd> */
    private array $propuestas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repoPropuesta = $GLOBALS['container']->get(PropuestaEncargoSacdRepositoryInterface::class);
        if (!$this->repoPropuesta->existenLasTablas()) {
            $this->markTestSkipped('No existe propuesta_encargos_sacd en la base de pruebas.');
        }
        $this->repository = $GLOBALS['container']->get(PropuestaEncargoSacdHorarioRepositoryInterface::class);
        $this->factory = new EncargoSacdHorarioFactory();
        $this->pdo = GlobalPdo::get('oDBE');
        $this->idsHorario = [];
        $this->propuestas = [];
    }

    public function tearDown(): void
    {
        foreach ($this->idsHorario as $id) {
            $this->pdo->exec("DELETE FROM propuesta_encargo_sacd_horario WHERE id_item = $id");
        }
        foreach ($this->propuestas as $p) {
            $this->repoPropuesta->Eliminar($p);
        }
        parent::tearDown();
    }

    private function crearPropuesta(): PropuestaEncargoSacd
    {
        $p = (new PropuestaEncargoSacdFactory())->createSimple();
        $this->assertTrue($this->repoPropuesta->Guardar($p), $this->repoPropuesta->getErrorTxt());
        $this->propuestas[] = $p;

        return $p;
    }

    private function horario(?int $idTarea = null, ?int $id = null): EncargoSacdHorario
    {
        $h = $this->factory->createSimple($id);
        $h->setId_item_tarea_sacd($idTarea);

        return $h;
    }

    private function guardar(EncargoSacdHorario $h): EncargoSacdHorario
    {
        $this->assertTrue($this->repository->Guardar($h), $this->repository->getErrorTxt());
        $this->idsHorario[] = $h->getId_item();

        return $h;
    }

    private function leer(int $idItem): ?EncargoSacdHorario
    {
        $res = $this->repository->matching(Criteria::create()->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdItem, $idItem)));

        return $res[0] ?? null;
    }

    public function test_guardar_nuevo_y_leer_todos_los_campos(): void
    {
        $tarea = $this->crearPropuesta();
        $h = $this->horario($tarea->getId_item());
        $h->setF_fin(new DateTimeLocal('2024-06-30'));
        $h->setDiaRefVo(new DiaRefCode('v'));
        $h->setDia_num(2);
        $h->setMasMenosVo(new MasMenosCode('+'));
        $h->setDia_inc(3);
        $h->setH_ini(TimeLocal::fromString('09:30:00'));
        $h->setH_fin(TimeLocal::fromString('21:05:00'));
        $this->guardar($h);

        $leido = $this->leer($h->getId_item());

        $this->assertNotNull($leido);
        $this->assertSame(3, $leido->getId_enc());
        $this->assertSame(10011, $leido->getId_nom());
        $this->assertSame('2023-04-05', $leido->getF_ini()?->format('Y-m-d'));
        $this->assertSame('2024-06-30', $leido->getF_fin()?->format('Y-m-d'));
        $this->assertSame('v', $leido->getDia_ref());
        $this->assertSame(2, $leido->getDia_num());
        $this->assertSame('+', $leido->getMas_menos());
        $this->assertSame(3, $leido->getDia_inc());
        $this->assertSame('09:30:00', $leido->getH_ini()?->format('H:i:s'));
        $this->assertSame('21:05:00', $leido->getH_fin()?->format('H:i:s'));
        $this->assertSame($tarea->getId_item(), $leido->getId_item_tarea_sacd());
    }

    public function test_guardar_con_campos_opcionales_a_null(): void
    {
        $h = $this->guardar($this->horario());

        $leido = $this->leer($h->getId_item());

        $this->assertNotNull($leido);
        $this->assertNull($leido->getF_fin());
        $this->assertNull($leido->getDia_ref());
        $this->assertNull($leido->getDia_num());
        $this->assertNull($leido->getMas_menos());
        $this->assertNull($leido->getH_ini());
        $this->assertNull($leido->getH_fin());
        $this->assertNull($leido->getId_item_tarea_sacd());
    }

    public function test_guardar_con_id_cero_asigna_id_de_la_secuencia(): void
    {
        $h = $this->horario(null, 0);

        $this->guardar($h);

        $this->assertGreaterThan(0, $h->getId_item());
        $this->assertNotNull($this->leer($h->getId_item()));
    }

    public function test_actualizar_existente_no_duplica_la_fila(): void
    {
        $h = $this->guardar($this->horario());

        $h->setId_nom(10012);
        $h->setH_ini(TimeLocal::fromString('08:00:00'));
        $h->setDia_num(5);
        $this->assertTrue($this->repository->Guardar($h), $this->repository->getErrorTxt());

        $leido = $this->leer($h->getId_item());
        $this->assertSame(10012, $leido->getId_nom());
        $this->assertSame('08:00:00', $leido->getH_ini()?->format('H:i:s'));
        $this->assertSame(5, $leido->getDia_num());
        $this->assertCount(
            1,
            $this->repository->matching(Criteria::create()->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdItem, $h->getId_item())))
        );
    }

    public function test_actualizar_hora_a_null(): void
    {
        $h = $this->horario();
        $h->setH_ini(TimeLocal::fromString('08:00:00'));
        $this->guardar($h);

        $h->setH_ini(null);
        $this->assertTrue($this->repository->Guardar($h), $this->repository->getErrorTxt());

        $this->assertNull($this->leer($h->getId_item())->getH_ini());
    }

    public function test_tarea_inexistente_viola_la_clave_foranea(): void
    {
        $h = $this->horario(2147000000);
        $this->idsHorario[] = $h->getId_item();

        try {
            $this->repository->Guardar($h);
            $this->fail('id_item_tarea_sacd debe existir en propuesta_encargos_sacd');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('id_item_tarea_sacd_fkey', $e->getMessage());
        }
        $this->assertNull($this->leer($h->getId_item()));
    }

    public function test_borrar_la_propuesta_borra_sus_horarios_en_cascada(): void
    {
        $tarea = $this->crearPropuesta();
        $conTarea = $this->guardar($this->horario($tarea->getId_item()));
        $sinTarea = $this->guardar($this->horario());

        $this->assertTrue($this->repoPropuesta->Eliminar($tarea));

        $this->assertNull($this->leer($conTarea->getId_item()));
        $this->assertNotNull($this->leer($sinTarea->getId_item()), 'los horarios sin tarea no se ven afectados');
    }

    public function test_cambiar_sacd_solo_afecta_al_encargo_y_sacd_indicados(): void
    {
        $objetivo = $this->horario();
        $objetivo->setId_enc(9901);
        $objetivo->setId_nom(10011);
        $otroSacd = $this->horario();
        $otroSacd->setId_enc(9901);
        $otroSacd->setId_nom(10050);
        $otroEncargo = $this->horario();
        $otroEncargo->setId_enc(9902);
        $otroEncargo->setId_nom(10011);
        foreach ([$objetivo, $otroSacd, $otroEncargo] as $h) {
            $this->guardar($h);
        }

        $this->assertTrue($this->repository->cambiarSacd(9901, 10011, 10099));

        $this->assertSame(10099, $this->leer($objetivo->getId_item())->getId_nom());
        $this->assertSame(10050, $this->leer($otroSacd->getId_item())->getId_nom());
        $this->assertSame(10011, $this->leer($otroEncargo->getId_item())->getId_nom());
    }

    public function test_cambiar_sacd_sin_coincidencias_devuelve_true_y_no_cambia_nada(): void
    {
        $h = $this->horario();
        $h->setId_enc(9903);
        $this->guardar($h);

        $this->assertTrue($this->repository->cambiarSacd(9903, 10777, 10099));

        $this->assertSame(10011, $this->leer($h->getId_item())->getId_nom());
    }

    public function test_get_new_id_es_creciente(): void
    {
        $a = $this->repository->getNewId();
        $b = $this->repository->getNewId();

        $this->assertGreaterThan(0, $a);
        $this->assertGreaterThan($a, $b);
    }

    public function test_matching_por_sacd_f_fin_null_y_orden_descendente_por_f_ini(): void
    {
        $viejo = $this->horario();
        $viejo->setId_enc(9904);
        $viejo->setId_nom(10666);
        $viejo->setF_ini(new DateTimeLocal('2022-01-01'));
        $nuevo = $this->horario();
        $nuevo->setId_enc(9904);
        $nuevo->setId_nom(10666);
        $nuevo->setF_ini(new DateTimeLocal('2024-01-01'));
        $cerrado = $this->horario();
        $cerrado->setId_enc(9904);
        $cerrado->setId_nom(10666);
        $cerrado->setF_ini(new DateTimeLocal('2023-01-01'));
        $cerrado->setF_fin(new DateTimeLocal('2023-12-31'));
        foreach ([$viejo, $nuevo, $cerrado] as $h) {
            $this->guardar($h);
        }

        $res = $this->repository->matching(
            Criteria::create()
                ->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdNom, 10666))
                ->andWhere(Filter::isNull(PropuestaEncargoSacdHorarioField::FFin))
                ->orderBy(PropuestaEncargoSacdHorarioField::FIni, OrderDirection::Desc)
        );

        $this->assertSame(
            [$nuevo->getId_item(), $viejo->getId_item()],
            array_map(static fn(EncargoSacdHorario $h) => $h->getId_item(), $res)
        );
    }

    public function test_matching_sin_resultados(): void
    {
        $this->assertSame([], $this->repository->matching(
            Criteria::create()->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdItem, 2147000003))
        ));
    }
}
