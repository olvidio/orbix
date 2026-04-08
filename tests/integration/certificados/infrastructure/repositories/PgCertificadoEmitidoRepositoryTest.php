<?php

namespace Tests\integration\certificados\infrastructure\persistence\postgresql;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use Tests\factories\certificados\CertificadoEmitidoFactory;
use Tests\myTest;

class PgCertificadoEmitidoRepositoryTest extends myTest
{
    private CertificadoEmitidoRepositoryInterface $repository;
    private CertificadoEmitidoFactory $factory;

    private mixed $session_org;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new CertificadoEmitidoFactory();

        // TODO: hay que cambiar la conexión a una region que tenga la tabla e_certificados_rstgr
        // Lo usa el setConnection
        putenv("UBICACION=sv");
        $this->session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'H-Hv';

        $this->repository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
        $oDBdst = $this->setConexion('H-Hv');
        $this->repository->setoDbl($oDBdst);

    }

    public function test_guardar_nuevo_certificadoEmitido()
    {
        // Crear instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();

        // Guardar
        $result = $this->repository->Guardar($oCertificadoEmitido);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCertificadoEmitidoGuardado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoGuardado);
        $this->assertEquals($id, $oCertificadoEmitidoGuardado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoGuardado);
    }

    public function test_actualizar_certificadoEmitido_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Crear otra instancia con datos diferentes para actualizar
        $oCertificadoEmitidoUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCertificadoEmitidoUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCertificadoEmitidoActualizado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoActualizado);

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Buscar por ID
        $oCertificadoEmitidoEncontrado = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoEncontrado);
        $this->assertInstanceOf(CertificadoEmitido::class, $oCertificadoEmitidoEncontrado);
        $this->assertEquals($id, $oCertificadoEmitidoEncontrado->getId_item());

        // Limpiar
        $this->repository->Eliminar($oCertificadoEmitidoEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $oCertificadoEmitido = $this->repository->findById($id_inexistente);

        $this->assertNull($oCertificadoEmitido);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_item', $aDatos);
        $this->assertEquals($id, $aDatos['id_item']);

        // Limpiar
        $oCertificadoEmitidoParaborrar = $this->repository->findById($id);
        $this->repository->Eliminar($oCertificadoEmitidoParaborrar);
    }

    public function test_datos_by_id_no_existente()
    {
        $id_inexistente = 99999999;
        $aDatos = $this->repository->datosById($id_inexistente);

        $this->assertFalse($aDatos);
    }

    public function test_eliminar_certificadoEmitido()
    {
        // Crear y guardar instancia usando factory
        $oCertificadoEmitido = $this->factory->createSimple();
        $id = $oCertificadoEmitido->getId_item();
        $this->repository->Guardar($oCertificadoEmitido);

        // Verificar que existe
        $oCertificadoEmitidoExiste = $this->repository->findById($id);
        $this->assertNotNull($oCertificadoEmitidoExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCertificadoEmitidoExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCertificadoEmitidoEliminado = $this->repository->findById($id);
        $this->assertNull($oCertificadoEmitidoEliminado);
    }

    public function test_get_certificados_sin_filtros()
    {
        $result = $this->repository->getCertificados();

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

    ///////////// Conexiones DB. Copiado del TrasladoDl ////////////////////////
    private function setConexion($esquema, $exterior = FALSE): \PDO
    {

        if (ConfigGlobal::mi_sfsv() === 2) {
            $database = 'sf';
            if ($exterior) {
                $database = 'sf-e';
            }
            if (ConfigGlobal::mi_region_dl() !== $esquema) {
                $esquema = 'restof';
            }
        } else {
            $database = 'sv';
            if ($exterior) {
                $database = 'sv-e';
            }
            // dlp?
            $oDBPropiedades = new DBPropiedades();
            $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
            // añadir el H-Hv
            $aEsquemas['H-Hv'] = 'H-Hv';

            if (!in_array($esquema, $aEsquemas, true)) {
                $esquema = 'restov';
            }
        }

        $oConfigDB = new ConfigDB($database);
        $config = $oConfigDB->getEsquema($esquema);

        return (new DBConnection($config))->getPDO();
    }

        /**
     * Runs at the end of every test.
     */
    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }
}
