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

            $this->assertEquals($Anuncio->getUsuarioCreadorVo(),$Anuncio2->getUsuarioCreadorVo());
            $this->assertEquals($Anuncio->getEsquemaEmisorVo(), $Anuncio2->getEsquemaEmisorVo());
            $this->assertEquals($Anuncio->getEsquemaDestinoVo(),$Anuncio2->getEsquemaDestinoVo());
            $this->assertEquals($Anuncio->getTextoAnuncioVo(),  $Anuncio2->getTextoAnuncioVo());
            $this->assertEquals($Anuncio->getIdiomaVo(),        $Anuncio2->getIdiomaVo());
            $this->assertEquals($Anuncio->getTablonVo(),        $Anuncio2->getTablonVo());
            $this->assertEquals($Anuncio->getT_anotado(),      $Anuncio2->getT_anotado());
            //$this->assertEquals($Anuncio->getT_eliminado(),    $Anuncio2->getT_eliminado());
            $this->assertEquals($Anuncio->getCategoriaVo(),     $Anuncio2->getCategoriaVo());

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