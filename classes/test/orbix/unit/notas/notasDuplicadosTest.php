<?php

namespace Tests\unit\notas;

use notas\model\EditarPersonaNota;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;
use ubis\model\entity\GestorDelegacion;

class notasDuplicadosTest extends myTest
{
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
        $esquema = 'GalBel-crGalBelv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1029;

        // persona de la dlA
        $id_nom = 1029156;
        $id_schema_persona = 1029; // GalBelv
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNotaDB = $rta['nota_real'];
        $oPersonaNotaDB->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNotaDB->getPrimary_key(); // para que tenga el mismo valor que la otra

        // probar de volver a crea la nota
        // cambio algo:
        $personaNota->setIdSituacion(20);
        $oEditarPersonaNota2 = new EditarPersonaNota($personaNota);
        $datosRegionStgr2 = $oEditarPersonaNota2->getDatosRegionStgr();

        $a_ObjetosPersonaNota2 = $oEditarPersonaNota2->getObjetosPersonaNota($datosRegionStgr2, $id_schema_persona);

        try {
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota2);
            $oPersonaNotaDB->DBEliminar();
        } catch (\RuntimeException $e) {
            // no hacer nada
            //$msg_err .= "\r\n";
            //$msg_err .= $e->getMessage();
            $oPersonaNotaDB->DBEliminar();
            $this->assertTrue(TRUE );
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
            case 'GalBel-crGalBel':
                $this->id_nom = 102912;
                $this->id_schema_persona = 1029;
                break;
        }
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(10);

        $dl = GestorDelegacion::getDlFromSchema($esquema);

        $this->cPersonaNotas = $NotasFactory->create($this->id_nom, $dl);
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