<?php

namespace Tests\unit\notas;

use src\notas\application\EditarPersonaNota;
use src\notas\domain\value_objects\NotaSituacion;
use src\ubis\application\services\DelegacionUtils;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;

class notasDuplicadosTest extends myTest
{
    use BuscaPersonaNotasTrait;

    private string $session_org;

    private string $snew_esquema;
    private string $sreg_dl_org;
    private string $sreg_dl_dst;

    private int $id_nom;
    private array $cPersonaNotas;
    private int $id_schema_persona;

    public function __construct(string $name)
    {
        $this->generarNotas('H-dlb');

        parent::__construct($name);
    }

    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Lo usa el setConnection
        putenv("UBICACION=sv");
        $this->session_org = $_SESSION['session_auth']['esquema'];
        $this->limpiarNotasDeFactory([1036156]);
    }

    private function nuevoEditarPersonaNota(\src\notas\domain\entity\PersonaNota $oPersonaNota): EditarPersonaNota
    {
        $container = $GLOBALS['container'];
        return new EditarPersonaNota(
            $oPersonaNota,
            $container->get(\src\notas\domain\contracts\PersonaNotaRepositoryInterface::class),
            $container->get(\src\ubis\domain\contracts\DelegacionRepositoryInterface::class),
            $container->get(\src\utils_database\domain\contracts\DbSchemaRepositoryInterface::class),
            $container->get(\src\dossiers\domain\contracts\DossierRepositoryInterface::class),
            $container->get(\src\notas\domain\contracts\PersonaNotaDlRepositoryInterface::class),
        );
    }

    /**
     * Dado que existe una nota (id_nom + id_nivel) en e_notas_dl)
     * Al intentar grabar una nueva: mensaje de error
     * si se hace desde matriculas, anula acta.
     * @return void
     */
    public function test_nueva_nota_ya_existe()
    {
        //$this->expectException(RuntimeException::class);
        // generar un nota
        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema = 'Galbel-crGalbelv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1036;

        // persona de la dlA
        $id_nom = 1036156;
        $id_schema_persona = 1036; // Galbelv
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = $this->nuevoEditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);
        $repoPersonaNota = $a_ObjetosPersonaNota['repo_real'];

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNotaDB = $rta['nota_real'];

        // probar de volver a crear la nota
        // cambio algo:
        $personaNota->setIdSituacionVo(NotaSituacion::DESCONOCIDO);
        $oEditarPersonaNota2 = $this->nuevoEditarPersonaNota($personaNota);
        $datosRegionStgr2 = $oEditarPersonaNota2->getDatosRegionStgr();

        $a_ObjetosPersonaNota2 = $oEditarPersonaNota2->getReposPersonaNota($datosRegionStgr2, $id_schema_persona);
        $repoPersonaNota2 = $a_ObjetosPersonaNota2['repo_real'];

        // Antes el test acababa en `assertTrue(TRUE)` dentro del catch: pasaba tanto si se rechazaba el duplicado
        // como si no. Ahora exige la excepción (la limpieza la hacen setUp/tearDown).
        $this->assertNotNull($oPersonaNotaDB, 'la primera nota se guarda');
        try {
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota2);
            $this->fail('una segunda nota de la misma asignatura debe rechazarse');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString((string) $id_nom, $e->getMessage());
            $this->assertStringContainsString((string) $personaNota->getId_asignatura(), $e->getMessage());
        }

    }

    public function generarNotas(string $esquema): void
    {
        //  Generar notas para id_nom (debe existir, para que no dé error)!!!
        switch ($esquema) {
            case 'H-dlb':
                $this->id_nom = 100111832;
                $this->id_schema_persona = 1001;
                break;
            case 'M-crM':
                $this->id_nom = 10271837;
                $this->id_schema_persona = 1027;
                break;
            case 'Galbel-crGalbel':
                $this->id_nom = 103612;
                $this->id_schema_persona = 1036;
                break;
        }
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(10);

        $dl = DelegacionUtils::getDlFromSchema($esquema);

        $this->cPersonaNotas = $NotasFactory->create($this->id_nom, $dl);
    }


    /**
     * Runs at the end of every test.
     */
    protected function tearDown(): void
    {
        $this->limpiarNotasDeFactory([1036156]);
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }
}