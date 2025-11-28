<?php

namespace Tests\integration\tablonanuncios;

use Exception;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use Tests\factories\tablonanuncios\AnunciosFactory;
use Tests\myTest;

class anunciosTest extends myTest
{
    private string $session_org;

    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->session_org = $_SESSION['session_auth']['esquema'];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function test_guardar_anuncio(): void
    {
        $esquema = 'H-dlbv';
        $count = 5;

        $AnunciosFactory = new AnunciosFactory();
        $AnunciosFactory->setCount($count);

        $cAnuncios = $AnunciosFactory->create();
        $AnuncioRepository = $GLOBALS['container']->get(AnuncioRepositoryInterface::class);
        foreach ($cAnuncios as $Anuncio) {
            $uuid_item = $Anuncio->getUuid_item();

            $AnuncioRepository->Guardar($Anuncio);

            $Anuncio2 = $AnuncioRepository->findById($uuid_item);

            $this->assertEquals($Anuncio->getUsuarioCreador(),$Anuncio2->getUsuarioCreador());
            $this->assertEquals($Anuncio->getEsquemaEmisor(), $Anuncio2->getEsquemaEmisor());
            $this->assertEquals($Anuncio->getEsquemaDestino(),$Anuncio2->getEsquemaDestino());
            $this->assertEquals($Anuncio->getTextoAnuncio(),  $Anuncio2->getTextoAnuncio());
            $this->assertEquals($Anuncio->getIdioma(),        $Anuncio2->getIdioma());
            $this->assertEquals($Anuncio->getTablon(),        $Anuncio2->getTablon());
            $this->assertEquals($Anuncio->getTanotado(),      $Anuncio2->getTanotado());
            //$this->assertEquals($Anuncio->getTeliminado(),    $Anuncio2->getTeliminado());
            $this->assertEquals($Anuncio->getCategoria(),     $Anuncio2->getCategoria());

            $AnuncioRepository->Eliminar($Anuncio2);
        }

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