<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\usuarios\domain\value_objects\Username;
use Tests\factories\usuarios\UsuariosFactory;
use Tests\myTest;

class PgUsuarioGrupoRepositoryTest extends myTest
{
    private UsuarioGrupoRepositoryInterface $repository;
    private UsuarioRepositoryInterface $usuarioRepository;
    private GrupoRepositoryInterface $grupoRepository;
    private UsuariosFactory $usuariosFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $this->usuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->grupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
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
     * Helper method para crear un grupo de prueba
     */
    private function crearGrupoPrueba(int $id_grupo): void
    {
        $oGrupo = new Grupo();
        $oGrupo->setId_usuario($id_grupo);
        $oGrupo->setUsuarioVo(new Username('test_group_' . $id_grupo));
        $oGrupo->setId_role(1);
        $this->grupoRepository->Guardar($oGrupo);
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

    /**
     * Helper method para eliminar un grupo de prueba
     */
    private function eliminarGrupoPrueba(int $id_grupo): void
    {
        $oGrupo = $this->grupoRepository->findById($id_grupo);
        if ($oGrupo !== null) {
            $this->grupoRepository->Eliminar($oGrupo);
        }
    }

    public function test_guardar_nuevo_usuario_grupo()
    {
        // Generar ids únicos para evitar conflictos
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);

        // Guardar el usuario-grupo
        $result = $this->repository->Guardar($oUsuarioGrupo);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $cUsuarioGrupo = $this->repository->getUsuariosGrupos([
            'id_usuario' => $id_usuario,
            'id_grupo' => $id_grupo
        ]);
        $this->assertNotEmpty($cUsuarioGrupo);
        $this->assertEquals($id_usuario, $cUsuarioGrupo[0]->getId_usuario());
        $this->assertEquals($id_grupo, $cUsuarioGrupo[0]->getId_grupo());

        // Limpiar
        $this->repository->Eliminar($oUsuarioGrupo);
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un usuario-grupo
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        $this->repository->Guardar($oUsuarioGrupo);

        // Buscar por ID de usuario
        $oUsuarioGrupoEncontrado = $this->repository->findById($id_usuario);

        $this->assertNotNull($oUsuarioGrupoEncontrado);
        $this->assertInstanceOf(UsuarioGrupo::class, $oUsuarioGrupoEncontrado);
        $this->assertEquals($id_usuario, $oUsuarioGrupoEncontrado->getId_usuario());

        // Limpiar
        $this->repository->Eliminar($oUsuarioGrupo);
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oUsuarioGrupo = $this->repository->findById($id_inexistente);

        $this->assertNull($oUsuarioGrupo);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un usuario-grupo
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        $this->repository->Guardar($oUsuarioGrupo);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_usuario);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_usuario', $aDatos);
        $this->assertArrayHasKey('id_grupo', $aDatos);
        $this->assertEquals($id_usuario, $aDatos['id_usuario']);

        // Limpiar
        $this->repository->Eliminar($oUsuarioGrupo);
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_eliminar_usuario_grupo()
    {
        // Crear y guardar un usuario-grupo
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        $this->repository->Guardar($oUsuarioGrupo);

        // Verificar que existe
        $oUsuarioGrupoExiste = $this->repository->findById($id_usuario);
        $this->assertNotNull($oUsuarioGrupoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oUsuarioGrupo);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oUsuarioGrupoEliminado = $this->repository->findById($id_usuario);
        $this->assertNull($oUsuarioGrupoEliminado);

        // Limpiar usuario y grupo
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_usuarios_grupos_sin_filtros()
    {
        $cUsuariosGrupos = $this->repository->getUsuariosGrupos();

        $this->assertIsArray($cUsuariosGrupos);

        if (!empty($cUsuariosGrupos)) {
            foreach ($cUsuariosGrupos as $oUsuarioGrupo) {
                $this->assertInstanceOf(UsuarioGrupo::class, $oUsuarioGrupo);
            }
        }
    }

    public function test_get_usuarios_grupos_con_filtro_id_usuario()
    {
        // Crear y guardar un usuario-grupo
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        $this->repository->Guardar($oUsuarioGrupo);

        // Buscar con filtro
        $cUsuariosGrupos = $this->repository->getUsuariosGrupos(['id_usuario' => $id_usuario]);

        $this->assertIsArray($cUsuariosGrupos);
        $this->assertNotEmpty($cUsuariosGrupos);

        // Verificar que todos tienen el mismo id_usuario
        foreach ($cUsuariosGrupos as $ug) {
            $this->assertEquals($id_usuario, $ug->getId_usuario());
        }

        // Limpiar
        $this->repository->Eliminar($oUsuarioGrupo);
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }

    public function test_get_usuarios_grupos_con_filtro_id_grupo()
    {
        // Crear y guardar un usuario-grupo
        $id_usuario = 9900000 + random_int(1000, 9999);
        $id_grupo = 9900000 + random_int(1000, 9999);

        // Crear usuario y grupo primero (foreign keys)
        $this->crearUsuarioPrueba($id_usuario);
        $this->crearGrupoPrueba($id_grupo);

        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        $this->repository->Guardar($oUsuarioGrupo);

        // Buscar con filtro
        $cUsuariosGrupos = $this->repository->getUsuariosGrupos(['id_grupo' => $id_grupo]);

        $this->assertIsArray($cUsuariosGrupos);
        $this->assertNotEmpty($cUsuariosGrupos);

        // Verificar que al menos uno tiene el id_grupo correcto
        $found = false;
        foreach ($cUsuariosGrupos as $ug) {
            if ($ug->getId_grupo() === $id_grupo) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Limpiar
        $this->repository->Eliminar($oUsuarioGrupo);
        $this->eliminarGrupoPrueba($id_grupo);
        $this->eliminarUsuarioPrueba($id_usuario);
    }
}
