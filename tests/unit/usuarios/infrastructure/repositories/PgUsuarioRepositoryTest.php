<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\infrastructure\repositories\PgUsuarioRepository;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\value_objects\Username;
use src\usuarios\domain\value_objects\Email;
use src\usuarios\domain\value_objects\Password;
use src\usuarios\domain\value_objects\IdPau;
use src\usuarios\domain\value_objects\NombreUsuario;
use src\usuarios\domain\value_objects\Secret2FA;
use Tests\myTest;

class PgUsuarioRepositoryTest extends myTest
{
    /**
     * @var PgUsuarioRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new PgUsuarioRepository();
    }

    /**
     * This is a placeholder test. In a real-world scenario, you would mock the database connection
     * and test the repository methods properly.
     */
    public function test_repository_instance()
    {
        $this->assertInstanceOf(PgUsuarioRepository::class, $this->repository);
    }

    /**
     * Note: The following tests are commented out because they require a database connection
     * and proper mocking, which is beyond the scope of this basic test suite.
     */

    public function test_get_array_usuarios()
    {
        // Mock the database connection and result
        // ...

        $result = $this->repository->getArrayUsuarios();
        $this->assertIsArray($result);
    }

    public function test_get_usuarios()
    {
        // Mock the database connection and result
        // ...

        $result = $this->repository->getUsuarios();
        $this->assertIsArray($result);
    }

    public function test_eliminar()
    {
        // Mock the database connection and result
        // ...

        $usuario = new Usuario();
        $usuario->setId_usuario(1);
        $result = $this->repository->Eliminar($usuario);
        $this->assertTrue($result);
    }

    public function test_guardar()
    {
        // Mock the database connection and result
        // ...

        $usuario = new Usuario();
        $usuario->setId_usuario(1);
        $usuario->setUsuario(new Username('testuser'));
        $result = $this->repository->Guardar($usuario);
        $this->assertTrue($result);
    }

    public function test_datos_by_id()
    {
        // Mock the database connection and result
        // ...

        $result = $this->repository->datosById(1);
        $this->assertIsArray($result);
    }

    public function test_find_by_id()
    {
        // Mock the database connection and result
        // ...

        $result = $this->repository->findById(1);
        $this->assertInstanceOf(Usuario::class, $result);
    }

    public function test_get_new_id()
    {
        // Mock the database connection and result
        // ...

        $result = $this->repository->getNewId();
        $this->assertIsNumeric($result);
    }
}