<?php

namespace Tests\integration\asistentes\infrastructure\repositories;

use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

class PgAsistenteExRepositoryTest extends myTest
{
    private AsistenteRepositoryInterface $repository;
    private AsistenteFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(AsistenteExRepositoryInterface::class);
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

    /* en la tabla actual, dl_responsable tiene valores vacío y da error
    public function test_get_asistentes_sin_filtros()
    {
        $result = $this->repository->getAsistentes();
        
        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }
    */

}
