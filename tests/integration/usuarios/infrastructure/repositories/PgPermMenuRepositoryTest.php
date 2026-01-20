<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\PermMenu;
use Tests\factories\usuarios\UsuariosFactory;
use Tests\myTest;

class PgPermMenuRepositoryTest extends myTest
{
    private PermMenuRepositoryInterface $repository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private UsuariosFactory $usuariosFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
        $this->usuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->usuariosFactory = new UsuariosFactory();
    }

    /**
     * Helper method para crear un usuario de prueba
     */
    private function crearUsuarioPrueba(int $id_usuario): void
    {
        $oUsuario = $this->usuariosFactory->createSimple($id_usuario, 'test_user_' . $id_usuario);
        $this->usuarioRepository->Guardar($oUsuario);
    }

    /**
     * Helper method para eliminar un usuario de prueba
     */
    private function eliminarUsuarioPrueba(int $id_usuario): void
    {
        $oUsuario = $this->usuarioRepository->findById($id_usuario);
        if ($oUsuario !== null) {
            $this->usuarioRepository->Eliminar($oUsuario);
        }
    }

    public function test_guardar_nuevo_perm_menu()
    {
        // Generar ids únicos para evitar conflictos
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(15); // Ejemplo de permisos

        // Guardar el permiso de menu
        $result = $this->repository->Guardar($oPermMenu);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oPermMenuGuardado = $this->repository->findById($id_item);
        $this->assertNotNull($oPermMenuGuardado);
        $this->assertEquals($id_item, $oPermMenuGuardado->getId_item());
        $this->assertEquals($id_usuario, $oPermMenuGuardado->getId_usuario());
        $this->assertEquals(15, $oPermMenuGuardado->getMenu_perm());

        // Limpiar
        $this->repository->Eliminar($oPermMenuGuardado);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_actualizar_perm_menu_existente()
    {
        // Crear y guardar un permiso de menu
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(7);
        $this->repository->Guardar($oPermMenu);

        // Modificar el permiso
        $oPermMenu->setMenu_perm(31);

        // Actualizar
        $result = $this->repository->Guardar($oPermMenu);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oPermMenuActualizado = $this->repository->findById($id_item);
        $this->assertEquals(31, $oPermMenuActualizado->getMenu_perm());

        // Limpiar
        $this->repository->Eliminar($oPermMenuActualizado);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un permiso de menu
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(3);
        $this->repository->Guardar($oPermMenu);

        // Buscar por ID
        $oPermMenuEncontrado = $this->repository->findById($id_item);

        $this->assertNotNull($oPermMenuEncontrado);
        $this->assertInstanceOf(PermMenu::class, $oPermMenuEncontrado);
        $this->assertEquals($id_item, $oPermMenuEncontrado->getId_item());
        $this->assertEquals($id_usuario, $oPermMenuEncontrado->getId_usuario());

        // Limpiar
        $this->repository->Eliminar($oPermMenuEncontrado);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oPermMenu = $this->repository->findById($id_inexistente);

        $this->assertNull($oPermMenu);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un permiso de menu
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(12);
        $this->repository->Guardar($oPermMenu);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_item);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertArrayHasKey('id_usuario', $aDatos);
        $this->assertArrayHasKey('menu_perm', $aDatos);
        $this->assertEquals($id_item, $aDatos['id_item']);
        $this->assertEquals($id_usuario, $aDatos['id_usuario']);

        // Limpiar
        $oPermMenuBuscado = $this->repository->findById($id_item);
        $this->repository->Eliminar($oPermMenuBuscado);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_eliminar_perm_menu()
    {
        // Crear y guardar un permiso de menu
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(5);
        $this->repository->Guardar($oPermMenu);

        // Verificar que existe
        $oPermMenuExiste = $this->repository->findById($id_item);
        $this->assertNotNull($oPermMenuExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPermMenu);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPermMenuEliminado = $this->repository->findById($id_item);
        $this->assertNull($oPermMenuEliminado);

        // Limpiar usuario
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_perm_menus_sin_filtros()
    {
        $cPermMenus = $this->repository->getPermMenus();

        $this->assertIsArray($cPermMenus);

        if (!empty($cPermMenus)) {
            foreach ($cPermMenus as $oPermMenu) {
                $this->assertInstanceOf(PermMenu::class, $oPermMenu);
            }
        }
    }

    public function test_get_perm_menus_con_filtro_id_usuario()
    {
        // Crear y guardar un permiso de menu
        $id_item = 9900000 + rand(1000, 9999);
        $id_usuario = 9900000 + rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPermMenu = new PermMenu();
        $oPermMenu->setId_item($id_item);
        $oPermMenu->setId_usuario($id_usuario);
        $oPermMenu->setMenu_perm(8);
        $this->repository->Guardar($oPermMenu);

        // Buscar con filtro
        $cPermMenus = $this->repository->getPermMenus(['id_usuario' => $id_usuario]);

        $this->assertIsArray($cPermMenus);
        $this->assertNotEmpty($cPermMenus);

        // Verificar que al menos uno tiene el id_usuario correcto
        $found = false;
        foreach ($cPermMenus as $pm) {
            if ($pm->getId_usuario() === $id_usuario) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Limpiar
        $this->repository->Eliminar($oPermMenu);

        // Limpiar usuario
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_new_id()
    {
        $newId = $this->repository->getNewId();

        $this->assertNotNull($newId);
        $this->assertIsNumeric($newId);
        $this->assertGreaterThan(0, $newId);
    }
}
