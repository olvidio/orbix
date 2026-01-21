<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;
use Tests\factories\usuarios\UsuariosFactory;
use Tests\myTest;

class PgUsuarioRepositoryTest extends myTest
{
    private UsuarioRepositoryInterface $repository;
    private UsuariosFactory $usuariosFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->usuariosFactory = new UsuariosFactory();
    }

    public function test_get_array_usuarios()
    {
        $aUsuarios = $this->repository->getArrayUsuarios();

        $this->assertIsArray($aUsuarios);
        $this->assertNotEmpty($aUsuarios);

        // Verificar que el formato es correcto (id => username)
        foreach ($aUsuarios as $id => $username) {
            $this->assertIsInt($id);
            $this->assertIsString($username);
        }
    }

    public function test_guardar_nuevo_usuario()
    {
        // Generar un id único para evitar conflictos
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'testuser_' . $id_usuario);

        // Guardar el usuario
        $result = $this->repository->Guardar($oUsuario);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oUsuarioGuardado = $this->repository->findById($id_usuario);
        $this->assertNotNull($oUsuarioGuardado);
        $this->assertEquals($id_usuario, $oUsuarioGuardado->getId_usuario());
        $this->assertEquals('testuser_' . $id_usuario, $oUsuarioGuardado->getUsuarioAsString());
        $this->assertEquals('test@example.com', $oUsuarioGuardado->getEmailAsString());

        // Limpiar
        $this->repository->Eliminar($oUsuarioGuardado);
    }

    public function test_actualizar_usuario_existente()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'original_user');
        $this->repository->Guardar($oUsuario);

        // Modificar el usuario
        $oUsuario->setEmailVo('updated@example.com');
        $oUsuario->setNomUsuarioVo('Updated User');

        // Actualizar
        $result = $this->repository->Guardar($oUsuario);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oUsuarioActualizado = $this->repository->findById($id_usuario);
        $this->assertEquals('updated@example.com', $oUsuarioActualizado->getEmailAsString());
        $this->assertEquals('Updated User', $oUsuarioActualizado->getNomUsuarioAsString());

        // Limpiar
        $this->repository->Eliminar($oUsuarioActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'findme_user');
        $this->repository->Guardar($oUsuario);

        // Buscar por ID
        $oUsuarioEncontrado = $this->repository->findById($id_usuario);

        $this->assertNotNull($oUsuarioEncontrado);
        $this->assertInstanceOf(Usuario::class, $oUsuarioEncontrado);
        $this->assertEquals($id_usuario, $oUsuarioEncontrado->getId_usuario());
        $this->assertEquals('findme_user', $oUsuarioEncontrado->getUsuarioAsString());

        // Limpiar
        $this->repository->Eliminar($oUsuarioEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oUsuario = $this->repository->findById($id_inexistente);

        $this->assertNull($oUsuario);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'datos_user');
        $this->repository->Guardar($oUsuario);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_usuario);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_usuario', $aDatos);
        $this->assertArrayHasKey('usuario', $aDatos);
        $this->assertEquals($id_usuario, $aDatos['id_usuario']);
        $this->assertEquals('datos_user', $aDatos['usuario']);

        // Limpiar
        $oUsuarioBuscado = $this->repository->findById($id_usuario);
        $this->repository->Eliminar($oUsuarioBuscado);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_usuario()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'delete_user');
        $this->repository->Guardar($oUsuario);

        // Verificar que existe
        $oUsuarioExiste = $this->repository->findById($id_usuario);
        $this->assertNotNull($oUsuarioExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oUsuario);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oUsuarioEliminado = $this->repository->findById($id_usuario);
        $this->assertNull($oUsuarioEliminado);
    }

    public function test_get_usuarios_sin_filtros()
    {
        $cUsuarios = $this->repository->getUsuarios();

        $this->assertIsArray($cUsuarios);
        $this->assertNotEmpty($cUsuarios);

        foreach ($cUsuarios as $oUsuario) {
            $this->assertInstanceOf(Usuario::class, $oUsuario);
        }
    }

    public function test_get_usuarios_con_filtro_id()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'filter_user');
        $this->repository->Guardar($oUsuario);

        // Buscar con filtro
        $cUsuarios = $this->repository->getUsuarios(['id_usuario' => $id_usuario]);

        $this->assertIsArray($cUsuarios);
        $this->assertCount(1, $cUsuarios);
        $this->assertEquals($id_usuario, $cUsuarios[0]->getId_usuario());

        // Limpiar
        $this->repository->Eliminar($oUsuario);
    }

    public function test_get_usuarios_con_filtro_usuario()
    {
        // Crear y guardar un usuario
        $id_usuario = 9900000 + random_int(1000, 9999);
        // OJO en total solo 20 caracteres para el username
        $username = 'uniq_filter_' . $id_usuario;
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, $username);
        $this->repository->Guardar($oUsuario);

        // Buscar con filtro
        $cUsuarios = $this->repository->getUsuarios(['usuario' => $username]);

        $this->assertIsArray($cUsuarios);
        $this->assertCount(1, $cUsuarios);
        $this->assertEquals($username, $cUsuarios[0]->getUsuarioAsString());

        // Limpiar
        $this->repository->Eliminar($oUsuario);
    }

    public function test_get_usuarios_con_limite()
    {
        $limit = 5;
        $cUsuarios = $this->repository->getUsuarios(['_limit' => $limit]);

        $this->assertIsArray($cUsuarios);
        $this->assertLessThanOrEqual($limit, count($cUsuarios));
    }

    public function test_get_usuarios_con_orden()
    {
        $cUsuarios = $this->repository->getUsuarios(['_ordre' => 'usuario ASC', '_limit' => 10]);

        $this->assertIsArray($cUsuarios);
        $this->assertNotEmpty($cUsuarios);

        // Verificar que están ordenados
        $usuarios = array_map(fn($u) => $u->getUsuarioAsString(), $cUsuarios);
        $usuariosOrdenados = $usuarios;
        natcasesort($usuariosOrdenados);

        $this->assertEquals($usuariosOrdenados, $usuarios);
    }

    public function test_guardar_usuario_con_2fa()
    {
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'user_2fa');
        $oUsuario->setHas_2fa(true);
        $oUsuario->setSecret2faVo('TESTSECRET2FA123');

        $this->repository->Guardar($oUsuario);

        $oUsuarioGuardado = $this->repository->findById($id_usuario);
        $this->assertTrue($oUsuarioGuardado->isHas_2fa());
        $this->assertEquals('TESTSECRET2FA123', $oUsuarioGuardado->getSecret2faAsString());

        // Limpiar
        $this->repository->Eliminar($oUsuarioGuardado);
    }

    public function test_guardar_usuario_con_cambio_password()
    {
        $id_usuario = 9900000 + random_int(1000, 9999);
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'user_cambiopwd');
        $oUsuario->setCambio_password(true);

        $this->repository->Guardar($oUsuario);

        $oUsuarioGuardado = $this->repository->findById($id_usuario);
        $this->assertTrue($oUsuarioGuardado->isCambio_password());

        // Limpiar
        $this->repository->Eliminar($oUsuarioGuardado);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();

        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);

        // Verificar que empieza con 4 (según la lógica del getNewId)
        $idString = (string)$newId;
        $this->assertEquals('4', $idString[0]);
    }
}