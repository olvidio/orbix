<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\RoleName;
use src\usuarios\domain\value_objects\PauType;
use Tests\myTest;

class PgRoleRepositoryTest extends myTest
{
    private RoleRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
    }

    public function test_get_array_roles()
    {
        $aRoles = $this->repository->getArrayRoles();

        $this->assertIsArray($aRoles);
        $this->assertNotEmpty($aRoles);

        // Verificar que el formato es correcto (id => role_name)
        foreach ($aRoles as $id => $roleName) {
            $this->assertIsInt($id);
            $this->assertIsString($roleName);
        }
    }

    public function test_get_array_roles_pau()
    {
        $aRolesPau = $this->repository->getArrayRolesPau();

        $this->assertIsArray($aRolesPau);
        $this->assertNotEmpty($aRolesPau);

        // Verificar que el formato es correcto (id => pau_type)
        foreach ($aRolesPau as $id => $pauType) {
            $this->assertIsInt($id);
            $this->assertIsString($pauType);
        }
    }

    public function test_guardar_nuevo_role()
    {
        // Generar un id único para evitar conflictos
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('test_role_' . $id_role));
        $oRole->setSf(true);
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $oRole->setDmz(false);

        // Guardar el role
        $result = $this->repository->Guardar($oRole);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oRoleGuardado = $this->repository->findById($id_role);
        $this->assertNotNull($oRoleGuardado);
        $this->assertEquals($id_role, $oRoleGuardado->getId_role());
        $this->assertEquals('test_role_' . $id_role, $oRoleGuardado->getRoleAsString());
        $this->assertTrue($oRoleGuardado->isSf());
        $this->assertTrue($oRoleGuardado->isSv());
        $this->assertEquals(PauType::PAU_CDC, $oRoleGuardado->getPauAsString());

        // Limpiar
        $this->repository->Eliminar($oRoleGuardado);
    }

    public function test_actualizar_role_existente()
    {
        // Crear y guardar un role
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('original_role'));
        $oRole->setSf(true);
        $oRole->setSv(false);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $this->repository->Guardar($oRole);

        // Modificar el role
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_NOM));

        // Actualizar
        $result = $this->repository->Guardar($oRole);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oRoleActualizado = $this->repository->findById($id_role);
        $this->assertTrue($oRoleActualizado->isSv());
        $this->assertEquals(PauType::PAU_NOM, $oRoleActualizado->getPauAsString());

        // Limpiar
        $this->repository->Eliminar($oRoleActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un role
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('findme_role'));
        $oRole->setSf(true);
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $this->repository->Guardar($oRole);

        // Buscar por ID
        $oRoleEncontrado = $this->repository->findById($id_role);

        $this->assertNotNull($oRoleEncontrado);
        $this->assertInstanceOf(Role::class, $oRoleEncontrado);
        $this->assertEquals($id_role, $oRoleEncontrado->getId_role());
        $this->assertEquals('findme_role', $oRoleEncontrado->getRoleAsString());

        // Limpiar
        $this->repository->Eliminar($oRoleEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 999999;
        $oRole = $this->repository->findById($id_inexistente);

        $this->assertNull($oRole);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un role
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('datos_role'));
        $oRole->setSf(true);
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $this->repository->Guardar($oRole);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_role);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_role', $aDatos);
        $this->assertArrayHasKey('role', $aDatos);
        $this->assertEquals($id_role, $aDatos['id_role']);
        $this->assertEquals('datos_role', $aDatos['role']);

        // Limpiar
        $oRoleBuscado = $this->repository->findById($id_role);
        $this->repository->Eliminar($oRoleBuscado);
    }

    public function test_eliminar_role()
    {
        // Crear y guardar un role
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('delete_role'));
        $oRole->setSf(true);
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $this->repository->Guardar($oRole);

        // Verificar que existe
        $oRoleExiste = $this->repository->findById($id_role);
        $this->assertNotNull($oRoleExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oRole);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oRoleEliminado = $this->repository->findById($id_role);
        $this->assertNull($oRoleEliminado);
    }

    public function test_get_roles_sin_filtros()
    {
        $cRoles = $this->repository->getRoles();

        $this->assertIsArray($cRoles);
        $this->assertNotEmpty($cRoles);

        foreach ($cRoles as $oRole) {
            $this->assertInstanceOf(Role::class, $oRole);
        }
    }

    public function test_get_roles_con_filtro_id()
    {
        // Crear y guardar un role
        $id_role = 9900 + random_int(100, 999);

        $oRole = new Role();
        $oRole->setId_role($id_role);
        $oRole->setRoleVo(new RoleName('filter_role'));
        $oRole->setSf(true);
        $oRole->setSv(true);
        $oRole->setPauVo(new PauType(PauType::PAU_CDC));
        $this->repository->Guardar($oRole);

        // Buscar con filtro
        $cRoles = $this->repository->getRoles(['id_role' => $id_role]);

        $this->assertIsArray($cRoles);
        $this->assertCount(1, $cRoles);
        $this->assertEquals($id_role, $cRoles[0]->getId_role());

        // Limpiar
        $this->repository->Eliminar($oRole);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();

        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
        $this->assertGreaterThan(0, $newId);
    }
}
