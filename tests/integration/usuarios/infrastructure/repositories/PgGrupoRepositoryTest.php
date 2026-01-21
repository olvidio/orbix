<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\value_objects\Username;
use Tests\myTest;

class PgGrupoRepositoryTest extends myTest
{
    private GrupoRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
    }

    public function test_guardar_nuevo_grupo()
    {
        // Generar un id único para evitar conflictos
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('test_grupo_' . $id_grupo));
        $oGrupo->setId_role(1);

        // Guardar el grupo
        $result = $this->repository->Guardar($oGrupo);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oGrupoGuardado = $this->repository->findById($id_grupo);
        $this->assertNotNull($oGrupoGuardado);
        $this->assertEquals($id_grupo, $oGrupoGuardado->getId_usuario());
        $this->assertEquals('test_grupo_' . $id_grupo, $oGrupoGuardado->getUsuarioAsString());
        $this->assertEquals(1, $oGrupoGuardado->getId_role());

        // Limpiar
        $this->repository->Eliminar($oGrupoGuardado);
    }

    public function test_actualizar_grupo_existente()
    {
        // Crear y guardar un grupo
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('original_grupo'));
        $oGrupo->setId_role(1);
        $this->repository->Guardar($oGrupo);

        // Modificar el grupo
        $oGrupo->setId_role(2);

        // Actualizar
        $result = $this->repository->Guardar($oGrupo);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oGrupoActualizado = $this->repository->findById($id_grupo);
        $this->assertEquals(2, $oGrupoActualizado->getId_role());

        // Limpiar
        $this->repository->Eliminar($oGrupoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un grupo
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('findme_grupo'));
        $oGrupo->setId_role(1);
        $this->repository->Guardar($oGrupo);

        // Buscar por ID
        $oGrupoEncontrado = $this->repository->findById($id_grupo);

        $this->assertNotNull($oGrupoEncontrado);
        $this->assertInstanceOf(Grupo::class, $oGrupoEncontrado);
        $this->assertEquals($id_grupo, $oGrupoEncontrado->getId_usuario());
        $this->assertEquals('findme_grupo', $oGrupoEncontrado->getUsuarioAsString());

        // Limpiar
        $this->repository->Eliminar($oGrupoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oGrupo = $this->repository->findById($id_inexistente);

        $this->assertNull($oGrupo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un grupo
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('datos_grupo'));
        $oGrupo->setId_role(1);
        $this->repository->Guardar($oGrupo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_grupo);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_usuario', $aDatos);
        $this->assertArrayHasKey('usuario', $aDatos);
        $this->assertEquals($id_grupo, $aDatos['id_usuario']);
        $this->assertEquals('datos_grupo', $aDatos['usuario']);

        // Limpiar
        $oGrupoBuscado = $this->repository->findById($id_grupo);
        $this->repository->Eliminar($oGrupoBuscado);
    }

    public function test_eliminar_grupo()
    {
        // Crear y guardar un grupo
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('delete_grupo'));
        $oGrupo->setId_role(1);
        $this->repository->Guardar($oGrupo);

        // Verificar que existe
        $oGrupoExiste = $this->repository->findById($id_grupo);
        $this->assertNotNull($oGrupoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oGrupo);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oGrupoEliminado = $this->repository->findById($id_grupo);
        $this->assertNull($oGrupoEliminado);
    }

    public function test_get_grupos_sin_filtros()
    {
        $cGrupos = $this->repository->getGrupos();

        $this->assertIsArray($cGrupos);
        $this->assertNotEmpty($cGrupos);

        foreach ($cGrupos as $oGrupo) {
            $this->assertInstanceOf(Grupo::class, $oGrupo);
        }
    }

    public function test_get_grupos_con_filtro_id()
    {
        // Crear y guardar un grupo
        $id_grupo = 9900000 + random_int(1000, 9999);
        
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('filter_grupo'));
        $oGrupo->setId_role(1);
        $this->repository->Guardar($oGrupo);

        // Buscar con filtro
        $cGrupos = $this->repository->getGrupos(['id_usuario' => $id_grupo]);

        $this->assertIsArray($cGrupos);
        $this->assertCount(1, $cGrupos);
        $this->assertEquals($id_grupo, $cGrupos[0]->getId_usuario());

        // Limpiar
        $this->repository->Eliminar($oGrupo);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();

        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);

        // Verificar que empieza con 5 (según la lógica del getNewId para grupos)
        $idString = (string)$newId;
        $this->assertEquals('5', $idString[0]);
    }
}
