<?php

namespace Tests\integration\asistentes\infrastructure\persistence\postgresql;

use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use Tests\myTest;
use Tests\factories\asistentes\AsistenteFactory;

class PgAsistenteDlRepositoryTest extends myTest
{
    private AsistenteRepositoryInterface $repository;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsistenteDlRepositoryInterface::class);
        $this->factory = new AsistenteFactory();
    }

    public function test_guardar_nuevo_asistente()
    {
        // Crear instancia usando factory
        $oAsistente = $this->factory->createSimple();
        $id = $oAsistente->getAsistentePk();

        // Guardar. genera un evento. Se publica automáticamente y se registra en av_cambios.
        $result = $this->repository->Guardar($oAsistente);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oAsistenteGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oAsistenteGuardado);
        $this->assertEquals($id, $oAsistenteGuardado->getAsistentePk());

        // Limpiar
        $this->repository->Eliminar($oAsistenteGuardado);

        // Verificar que se registró en av_cambios
        // id_tipo_cambio = 1 -> INSERT
        $sql = "SELECT * FROM av_cambios 
            WHERE id_activ = :id_activ 
            AND (objeto = 'Asistente' OR objeto = 'AsistenteDl') 
            AND id_tipo_cambio = 1
            ORDER BY timestamp_cambio DESC LIMIT 1";

        $stmt = $GLOBALS['oDBPC']->prepare($sql);
        $stmt->execute(['id_activ' => $id->IdActiv()]);
        $cambio = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($cambio);
        $this->assertEquals(1, $cambio['id_tipo_cambio']);

    }

    public function test_actualizar_asistente_existente()
    {
        // Crear y guardar instancia usando factory
        $oAsistente = $this->factory->createSimple();
        $id = $oAsistente->getAsistentePk();
        $this->repository->Guardar($oAsistente);

        // Crear otra instancia con datos diferentes para actualizar
        $oAsistenteUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oAsistenteUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oAsistenteActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oAsistenteActualizado);

        // Limpiar
        $this->repository->Eliminar($oAsistenteActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oAsistente = $this->factory->createSimple();
        $id = $oAsistente->getAsistentePk();
        $this->repository->Guardar($oAsistente);

        // Buscar por ID
        $oAsistenteEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oAsistenteEncontrado);
        $this->assertInstanceOf(Asistente::class, $oAsistenteEncontrado);
        $this->assertEquals($id, $oAsistenteEncontrado->getAsistentePk());

        // Limpiar
        $this->repository->Eliminar($oAsistenteEncontrado);
    }


    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oAsistente = $this->factory->createSimple();
        $id = $oAsistente->getAsistentePk();
        $this->repository->Guardar($oAsistente);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_activ', $aDatos);
        $this->assertEquals($id->idActiv(), $aDatos['id_activ']);

        // Limpiar
        $oAsistenteParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oAsistenteParaborrar);
    }

    public function test_eliminar_asistente()
    {
        // Crear y guardar instancia usando factory
        $oAsistente = $this->factory->createSimple();
        $id = $oAsistente->getAsistentePk();
        $this->repository->Guardar($oAsistente);

        // Verificar que existe
        $oAsistenteExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oAsistenteExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oAsistenteExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oAsistenteEliminado = $this->repository->findByPk($id);
        $this->assertNull($oAsistenteEliminado);
    }


    private function asistenteConDatos(\src\asistentes\domain\value_objects\AsistentePk $pk, bool $marcas, string $sufijo, ?int $plaza): Asistente
    {
        $o = $this->factory->createSimple($pk);
        $o->setPropio($marcas);
        $o->setEst_ok($marcas);
        $o->setCfi($marcas);
        $o->setFalta($marcas);
        $o->setCfi_con($marcas ? 5 : null);
        $o->setEncargo('encargo ' . $sufijo);
        $o->setObserv('observ ' . $sufijo);
        $o->setObservEstVo('observ est ' . $sufijo);
        $o->setDl_responsable('dl' . $sufijo);
        $o->setIdTablaVo('dl');
        $o->setPlaza($plaza);
        $o->setPropietarioVo($plaza === null ? null : 'dl' . $sufijo . '>dl');

        return $o;
    }

    /** @return array<string, mixed> */
    private static function campos(Asistente $o): array
    {
        return [
            'propio' => $o->isPropio(),
            'est_ok' => $o->isEst_ok(),
            'cfi' => $o->isCfi(),
            'falta' => $o->isFalta(),
            'cfi_con' => $o->getCfi_con(),
            'encargo' => $o->getEncargo(),
            'observ' => $o->getObserv(),
            'observ_est' => $o->getObserv_est(),
            'dl_responsable' => $o->getDl_responsable(),
            'id_tabla' => $o->getId_tabla(),
            'plaza' => $o->getPlazaVo()?->value(),
            'propietario' => $o->getPropietarioVo()?->value(),
        ];
    }

    /**
     * Ida y vuelta de todos los campos, al crear y al actualizar con valores distintos (incluidos los nulos):
     * el test de «actualizar» guardaba dos veces los mismos datos y no comprobaba nada de lo actualizado.
     */
    public function test_ida_y_vuelta_de_todos_los_campos_al_crear_y_al_actualizar(): void
    {
        $pk = new \src\asistentes\domain\value_objects\AsistentePk(10018801, 10019801);
        try {
            $alta = $this->asistenteConDatos($pk, true, 'b', 4);
            $this->assertTrue($this->repository->Guardar($alta));
            $leido = $this->repository->findByPk($pk);
            $this->assertNotNull($leido);
            $this->assertSame(self::campos($alta), self::campos($leido));

            $cambio = $this->asistenteConDatos($pk, false, 'p', null);
            $this->assertTrue($this->repository->Guardar($cambio));
            $releido = $this->repository->findByPk($pk);
            $this->assertSame(self::campos($cambio), self::campos($releido));
            $this->assertSame([false, null, null, 'dlp'], [$releido->isPropio(), $releido->getCfi_con(), $releido->getPlazaVo(), $releido->getDl_responsable()]);
        } finally {
            $o = $this->repository->findByPk($pk);
            if ($o !== null) {
                $this->repository->Eliminar($o);
            }
        }
        $this->assertNull($this->repository->findByPk($pk));
    }

    /**
     * Hidrata filas reales de la tabla: un valor de BD que un VO no acepte (p. ej. un `dl_responsable` vacío, que
     * antes hacía fallar este test) se ve aquí y no solo en producción.
     */
    public function test_las_filas_reales_de_la_tabla_se_hidratan_sin_errores(): void
    {
        $result = $this->repository->matching(
            \src\shared\domain\criteria\Criteria::create()
                ->orderBy(\src\asistentes\domain\criteria\AsistenteField::IdActiv, \src\shared\domain\criteria\OrderDirection::Desc)
                ->limit(500)
        );

        $this->assertIsList($result);
        $this->assertContainsOnlyInstancesOf(Asistente::class, $result);
        $this->assertLessThanOrEqual(500, count($result));
    }

}
