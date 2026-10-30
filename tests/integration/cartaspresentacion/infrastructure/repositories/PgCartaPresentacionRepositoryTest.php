<?php

namespace Tests\integration\cartaspresentacion\infrastructure\persistence\postgresql;

use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\criteria\CartaPresentacionField;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use Tests\factories\cartaspresentacion\CartaPresentacionFactory;
use Tests\factories\ubis\CasaFactory;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

class PgCartaPresentacionRepositoryTest extends myTest
{
    private CartaPresentacionRepositoryInterface $repository;
    private CartaPresentacionFactory $factory;
    private CasaRepositoryInterface $casaRepository;
    private DireccionRepositoryInterface $direccionRepository;
    private array $casasCreadas = [];
    private array $direccionesCreadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(CartaPresentacionDlRepositoryInterface::class);
        $this->casaRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $this->direccionRepository = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
        $this->factory = new CartaPresentacionFactory();

        // Crear las casas/ubis necesarias para las foreign keys
        $casaFactory = new CasaFactory();
        $casa1 = $casaFactory->createSimple(-10019001);

        $this->casaRepository->Guardar($casa1);

        $this->casasCreadas[] = $casa1;

        // Crear las direcciones necesarias para las foreign keys
        $direccionFactory = new DireccionFactory();
        $dir1 = $direccionFactory->createSimple(-10019001);

        $this->direccionRepository->Guardar($dir1);

        $this->direccionesCreadas[] = $dir1;
    }

    public function test_guardar_nuevo_cartaPresentacion()
    {
        // Crear instancia usando factory
        $oCartaPresentacion = $this->factory->createSimple();
        $id = $oCartaPresentacion->getPresentacionPk();

        // Guardar
        $result = $this->repository->Guardar($oCartaPresentacion);
        $this->assertTrue($result);

        // Verificar que se guardó
        $oCartaPresentacionGuardado = $this->repository->findByPk($id);
        $this->assertNotNull($oCartaPresentacionGuardado);
        $this->assertEquals($id, $oCartaPresentacionGuardado->getPresentacionPk());

        // Limpiar
        $this->repository->Eliminar($oCartaPresentacionGuardado);
    }

    public function test_actualizar_cartaPresentacion_existente()
    {
        // Crear y guardar instancia usando factory
        $oCartaPresentacion = $this->factory->createSimple();
        $id = $oCartaPresentacion->getPresentacionPk();
        $this->repository->Guardar($oCartaPresentacion);

        // Crear otra instancia con datos diferentes para actualizar
        $oCartaPresentacionUpdated = $this->factory->createSimple($id);

        // Actualizar
        $result = $this->repository->Guardar($oCartaPresentacionUpdated);
        $this->assertTrue($result);

        // Verificar actualización
        $oCartaPresentacionActualizado = $this->repository->findByPk($id);
        $this->assertNotNull($oCartaPresentacionActualizado);

        // Limpiar
        $this->repository->Eliminar($oCartaPresentacionActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCartaPresentacion = $this->factory->createSimple();
        $id = $oCartaPresentacion->getPresentacionPk();
        $this->repository->Guardar($oCartaPresentacion);

        // Buscar por ID
        $oCartaPresentacionEncontrado = $this->repository->findByPk($id);
        $this->assertNotNull($oCartaPresentacionEncontrado);
        $this->assertInstanceOf(CartaPresentacion::class, $oCartaPresentacionEncontrado);
        $this->assertEquals($id, $oCartaPresentacionEncontrado->getPresentacionPk());

        // Limpiar
        $this->repository->Eliminar($oCartaPresentacionEncontrado);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar instancia usando factory
        $oCartaPresentacion = $this->factory->createSimple();
        $id = $oCartaPresentacion->getPresentacionPk();
        $this->repository->Guardar($oCartaPresentacion);

        // Obtener datos por ID
        $aDatos = $this->repository->datosByPk($id);
        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_direccion', $aDatos);
        $this->assertEquals($id->idDireccion(), $aDatos['id_direccion']);

        // Limpiar
        $oCartaPresentacionParaborrar = $this->repository->findByPk($id);
        $this->repository->Eliminar($oCartaPresentacionParaborrar);
    }

    public function test_eliminar_cartaPresentacion()
    {
        // Crear y guardar instancia usando factory
        $oCartaPresentacion = $this->factory->createSimple();
        $id = $oCartaPresentacion->getPresentacionPk();
        $this->repository->Guardar($oCartaPresentacion);

        // Verificar que existe
        $oCartaPresentacionExiste = $this->repository->findByPk($id);
        $this->assertNotNull($oCartaPresentacionExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oCartaPresentacionExiste);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oCartaPresentacionEliminado = $this->repository->findByPk($id);
        $this->assertNull($oCartaPresentacionEliminado);
    }

    public function test_matching_por_ubi()
    {
        $oCartaPresentacion = $this->factory->createSimple();
        $idUbi = $oCartaPresentacion->getId_ubi();
        $this->repository->Guardar($oCartaPresentacion);

        $criteria = Criteria::create()->where(Filter::eq(CartaPresentacionField::IdUbi, $idUbi));
        $nuevo = $this->repository->matching($criteria);

        $this->assertCount(1, $nuevo);
        $this->assertEquals($idUbi, $nuevo[0]->getId_ubi());

        $this->repository->Eliminar($oCartaPresentacion);
        $this->assertSame([], $this->repository->matching($criteria));
    }

    public function test_get_cartas_presentacion_sin_filtros()
    {
        $result = $this->repository->matching(Criteria::create());

        $this->assertIsArray($result);
        // TODO: Añadir más aserciones según la estructura esperada
    }

}
