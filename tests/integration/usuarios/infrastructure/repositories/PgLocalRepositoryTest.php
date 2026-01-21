<?php

namespace Tests\unit\usuarios\infrastructure\repositories;

use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;
use src\usuarios\domain\value_objects\IdLocale;
use src\usuarios\domain\value_objects\NombreLocale;
use src\usuarios\domain\value_objects\Idioma;
use src\usuarios\domain\value_objects\NombreIdioma;
use Tests\myTest;

class PgLocalRepositoryTest extends myTest
{
    private LocalRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
    }

    public function test_get_array_locales()
    {
        $aLocales = $this->repository->getArrayLocales();

        $this->assertIsArray($aLocales);
        $this->assertNotEmpty($aLocales);

        // Verificar que el formato es correcto (id_locale => nombre)
        foreach ($aLocales as $id => $nombre) {
            $this->assertIsString($id);
            $this->assertIsString($nombre);
        }
    }

    public function test_guardar_nuevo_local()
    {
        // Generar un id único para evitar conflictos
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Test Locale'));
        $oLocal->setIdiomaVo(new Idioma('es'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('Español'));
        $oLocal->setActive(true);

        // Guardar el local
        $result = $this->repository->Guardar($oLocal);
        $this->assertTrue($result);

        // Verificar que se guardó correctamente
        $oLocalGuardado = $this->repository->findById($id_locale);
        $this->assertNotNull($oLocalGuardado);
        $this->assertEquals($id_locale, $oLocalGuardado->getIdLocaleAsString());
        $this->assertEquals('Test Locale', $oLocalGuardado->getNomLocaleAsString());
        $this->assertEquals('es', $oLocalGuardado->getIdiomaAsString());
        $this->assertTrue($oLocalGuardado->isActive());

        // Limpiar
        $this->repository->Eliminar($oLocalGuardado);
    }

    public function test_actualizar_local_existente()
    {
        // Crear y guardar un local
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Original Locale'));
        $oLocal->setIdiomaVo(new Idioma('en'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('English'));
        $oLocal->setActive(true);
        $this->repository->Guardar($oLocal);

        // Modificar el local
        $oLocal->setNomLocaleVo(new NombreLocale('Updated Locale'));
        $oLocal->setActive(false);

        // Actualizar
        $result = $this->repository->Guardar($oLocal);
        $this->assertTrue($result);

        // Verificar que se actualizó
        $oLocalActualizado = $this->repository->findById($id_locale);
        $this->assertEquals('Updated Locale', $oLocalActualizado->getNomLocaleAsString());
        $this->assertFalse($oLocalActualizado->isActive());

        // Limpiar
        $this->repository->Eliminar($oLocalActualizado);
    }

    public function test_find_by_id_existente()
    {
        // Crear y guardar un local
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Find Me Locale'));
        $oLocal->setIdiomaVo(new Idioma('fr'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('Français'));
        $oLocal->setActive(true);
        $this->repository->Guardar($oLocal);

        // Buscar por ID
        $oLocalEncontrado = $this->repository->findById($id_locale);

        $this->assertNotNull($oLocalEncontrado);
        $this->assertInstanceOf(Local::class, $oLocalEncontrado);
        $this->assertEquals($id_locale, $oLocalEncontrado->getIdLocaleAsString());
        $this->assertEquals('Find Me Locale', $oLocalEncontrado->getNomLocaleAsString());

        // Limpiar
        $this->repository->Eliminar($oLocalEncontrado);
    }

    public function test_find_by_id_no_existente()
    {
        $id_inexistente = 'inexistente_' . random_int(1000, 9999);
        $oLocal = $this->repository->findById($id_inexistente);

        $this->assertNull($oLocal);
    }

    public function test_datos_by_id_existente()
    {
        // Crear y guardar un local
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Datos Locale'));
        $oLocal->setIdiomaVo(new Idioma('de'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('Deutsch'));
        $oLocal->setActive(true);
        $this->repository->Guardar($oLocal);

        // Obtener datos por ID
        $aDatos = $this->repository->datosById($id_locale);

        $this->assertIsArray($aDatos);
        $this->assertArrayHasKey('id_locale', $aDatos);
        $this->assertArrayHasKey('nom_locale', $aDatos);
        $this->assertEquals($id_locale, $aDatos['id_locale']);
        $this->assertEquals('Datos Locale', $aDatos['nom_locale']);

        // Limpiar
        $oLocalBuscado = $this->repository->findById($id_locale);
        $this->repository->Eliminar($oLocalBuscado);
    }

    public function test_eliminar_local()
    {
        // Crear y guardar un local
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Delete Locale'));
        $oLocal->setIdiomaVo(new Idioma('it'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('Italiano'));
        $oLocal->setActive(true);
        $this->repository->Guardar($oLocal);

        // Verificar que existe
        $oLocalExiste = $this->repository->findById($id_locale);
        $this->assertNotNull($oLocalExiste);

        // Eliminar
        $result = $this->repository->Eliminar($oLocal);
        $this->assertTrue($result);

        // Verificar que ya no existe
        $oLocalEliminado = $this->repository->findById($id_locale);
        $this->assertNull($oLocalEliminado);
    }

    public function test_get_locales_sin_filtros()
    {
        $cLocales = $this->repository->getLocales();

        $this->assertIsArray($cLocales);
        $this->assertNotEmpty($cLocales);

        foreach ($cLocales as $oLocal) {
            $this->assertInstanceOf(Local::class, $oLocal);
        }
    }

    public function test_get_locales_con_filtro_activos()
    {
        $cLocales = $this->repository->getLocales(['active' => true]);

        $this->assertIsArray($cLocales);
        $this->assertNotEmpty($cLocales);

        foreach ($cLocales as $oLocal) {
            $this->assertTrue($oLocal->isActive());
        }
    }

    public function test_get_locales_con_filtro_idioma()
    {
        // Crear y guardar un local con un idioma específico
        $id_locale = 'test_' . random_int(1000, 9999);

        $oLocal = new Local();
        $oLocal->setIdLocaleVo(new IdLocale($id_locale));
        $oLocal->setNomLocaleVo(new NombreLocale('Filter Locale'));
        $oLocal->setIdiomaVo(new Idioma('pt'));
        $oLocal->setNomIdiomaVo(new NombreIdioma('Português'));
        $oLocal->setActive(true);
        $this->repository->Guardar($oLocal);

        // Buscar con filtro
        $cLocales = $this->repository->getLocales(['idioma' => 'pt']);

        $this->assertIsArray($cLocales);
        $this->assertNotEmpty($cLocales);

        // Verificar que al menos uno tiene el idioma correcto
        $found = false;
        foreach ($cLocales as $loc) {
            if ($loc->getIdiomaAsString() === 'pt') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);

        // Limpiar
        $this->repository->Eliminar($oLocal);
    }
}
