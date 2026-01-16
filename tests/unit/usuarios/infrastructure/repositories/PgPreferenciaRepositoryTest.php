<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use Tests\factories\usuarios\UsuariosFactory;
use Tests\myTest;

class PgPreferenciaRepositoryTest extends myTest
{
    private PreferenciaRepositoryInterface $repository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private UsuariosFactory $usuariosFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
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

    public function test_guardar_nueva_preferencia()
    {
        // Generar un id único para evitar conflictos
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('valor_test'));

        // Guardar la preferencia
        $result = $this->repository->Guardar($oPreferencia);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oPreferenciaGuardada = $this->repository->findById($id_usuario, $tipo);
        $this->assertNotNull($oPreferenciaGuardada);
        $this->assertEquals($id_usuario, $oPreferenciaGuardada->getId_usuario());
        $this->assertEquals($tipo, $oPreferenciaGuardada->getTipoPreferenciaAsString());
        $this->assertEquals('valor_test', $oPreferenciaGuardada->getPreferenciaAsString());

        // Limpiar
        $this->repository->Eliminar($oPreferenciaGuardada);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_actualizar_preferencia_existente()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('valor_original'));
        $this->repository->Guardar($oPreferencia);

        // Modificar la preferencia
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('valor_actualizado'));

        // Actualizar
        $result = $this->repository->Guardar($oPreferencia);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oPreferenciaActualizada = $this->repository->findById($id_usuario, $tipo);
        $this->assertEquals('valor_actualizado', $oPreferenciaActualizada->getPreferenciaAsString());

        // Limpiar
        $this->repository->Eliminar($oPreferenciaActualizada);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('findme_valor'));
        $this->repository->Guardar($oPreferencia);

        // Buscar por ID
        $oPreferenciaEncontrada = $this->repository->findById($id_usuario, $tipo);

        $this->assertNotNull($oPreferenciaEncontrada);
        $this->assertInstanceOf(Preferencia::class, $oPreferenciaEncontrada);
        $this->assertEquals($id_usuario, $oPreferenciaEncontrada->getId_usuario());
        $this->assertEquals($tipo, $oPreferenciaEncontrada->getTipoPreferenciaAsString());
        $this->assertEquals('findme_valor', $oPreferenciaEncontrada->getPreferenciaAsString());

        // Limpiar
        $this->repository->Eliminar($oPreferenciaEncontrada);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $tipo_inexistente = 'tipo_inexistente';
        $oPreferencia = $this->repository->findById($id_inexistente, $tipo_inexistente);

        $this->assertNull($oPreferencia);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('datos_valor'));
        $this->repository->Guardar($oPreferencia);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_usuario, $tipo);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_usuario', $aDatos);
        $this->assertArrayHasKey('tipo', $aDatos);
        $this->assertArrayHasKey('preferencia', $aDatos);
        $this->assertEquals($id_usuario, $aDatos['id_usuario']);
        $this->assertEquals($tipo, $aDatos['tipo']);
        $this->assertEquals('datos_valor', $aDatos['preferencia']);

        // Limpiar
        $oPreferenciaBuscada = $this->repository->findById($id_usuario, $tipo);
        $this->repository->Eliminar($oPreferenciaBuscada);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_eliminar_preferencia()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('delete_valor'));
        $this->repository->Guardar($oPreferencia);

        // Verificar que existe
        $oPreferenciaExiste = $this->repository->findById($id_usuario, $tipo);
        $this->assertNotNull($oPreferenciaExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oPreferencia);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oPreferenciaEliminada = $this->repository->findById($id_usuario, $tipo);
        $this->assertNull($oPreferenciaEliminada);

        // Limpiar usuario
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_preferencias_sin_filtros()
    {
        $cPreferencias = $this->repository->getPreferencias();

        $this->assertIsArray($cPreferencias);

        if (!empty($cPreferencias)) {
            foreach ($cPreferencias as $oPreferencia) {
                $this->assertInstanceOf(Preferencia::class, $oPreferencia);
            }
        }
    }

    public function test_get_preferencias_con_filtro_id_usuario()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('filter_valor'));
        $this->repository->Guardar($oPreferencia);

        // Buscar con filtro
        $cPreferencias = $this->repository->getPreferencias(['id_usuario' => $id_usuario]);

        $this->assertIsArray($cPreferencias);
        $this->assertNotEmpty($cPreferencias);

        // Verificar que al menos una preferencia tiene el id_usuario correcto
        $found = false;
        foreach ($cPreferencias as $pref) {
            if ($pref->getId_usuario() === $id_usuario) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Limpiar
        $this->repository->Eliminar($oPreferencia);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_preferencias_con_filtro_tipo()
    {
        // Crear y guardar una preferencia
        $id_usuario = 9900000 + rand(1000, 9999);
        $tipo = 'test_pref_' . rand(1000, 9999);

        // Crear el usuario primero (foreign key)
        $this->crearUsuarioPrueba($id_usuario);

        $oPreferencia = new Preferencia();
        $oPreferencia->setId_usuario($id_usuario);
        $oPreferencia->setTipoVo(new TipoPreferencia($tipo));
        $oPreferencia->setPreferenciaVo(new ValorPreferencia('filter_tipo_valor'));
        $this->repository->Guardar($oPreferencia);

        // Buscar con filtro
        $cPreferencias = $this->repository->getPreferencias(['tipo' => $tipo]);

        $this->assertIsArray($cPreferencias);
        $this->assertNotEmpty($cPreferencias);
        $this->assertEquals($tipo, $cPreferencias[0]->getTipoPreferenciaAsString());

        // Limpiar
        $this->repository->Eliminar($oPreferencia);
        $this->eliminarUsuarioPrueba($id_usuario);
    }
}
