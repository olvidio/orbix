<?php

namespace Tests\integration\notas\infrastructure\persistence\postgresql;

use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\PersonaNotaPk;
use Tests\factories\notas\PersonaNotaFactory;
use Tests\myTest;

/**
 * Persona de paso: al emitir el certificado las notas se copian a e_notas_otra_region_stgr
 * con json_certificados y dejan de salir como pendientes en getNotasPersonasDePasoDeRegion.
 */
class CertificarNotasPersonaDePasoTest extends myTest
{
    private const ID_NOM = -987654;
    private const ID_NIVEL = 1223;
    private const ESQUEMA_REGION = 'H-Hv';

    private PersonaNotaDlRepositoryInterface $repoDl;
    private PersonaNotaOtraRegionStgrRepositoryInterface $repoOtra;
    private PersonaNotaRepositoryInterface $repoPadre;

    public function setUp(): void
    {
        parent::setUp();
        $this->repoDl = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $this->repoOtra = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => self::ESQUEMA_REGION]
        );
        $this->repoPadre = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        $pk = new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1);
        $otra = $this->repoOtra->findByPk($pk);
        if ($otra !== null) {
            $this->repoOtra->Eliminar($otra);
        }
        $dl = $this->repoDl->findById(self::ID_NOM, self::ID_NIVEL, 1);
        if ($dl !== null) {
            $this->repoDl->Eliminar($dl);
        }
        parent::tearDown();
    }

    private function idsPendientes(): array
    {
        $ids = [];
        foreach ($this->repoPadre->getNotasPersonasDePasoDeRegion(self::ESQUEMA_REGION) as $nota) {
            $ids[] = $nota->getId_nom() . ':' . $nota->getId_nivel();
        }

        return $ids;
    }

    public function test_certificar_copia_la_nota_y_la_saca_de_pendientes()
    {
        $nota = (new PersonaNotaFactory())->createSimple(new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1));
        $this->assertTrue($this->repoDl->Guardar($nota));
        $clave = self::ID_NOM . ':' . self::ID_NIVEL;

        $this->assertContains($clave, $this->idsPendientes());
        $this->assertNull($this->repoOtra->findByPk(new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1)));

        $this->repoOtra->addCertificado(self::ID_NOM, 'TEST 1/26', null);

        $copiada = $this->repoOtra->findByPk(new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1));
        $this->assertNotNull($copiada);
        $certs = $copiada->getJson_certificados(true);
        $this->assertSame('TEST 1/26', $certs[0]['certificado']);
        $this->assertSame('guardado', $certs[0]['estado']);
        $this->assertNotContains($clave, $this->idsPendientes());
        // La nota original sigue en la dl.
        $this->assertNotNull($this->repoDl->findById(self::ID_NOM, self::ID_NIVEL, 1));

        // Reemitir el mismo certificado no duplica el sello.
        $this->repoOtra->addCertificado(self::ID_NOM, 'TEST 1/26', null);
        $certs = $this->repoOtra->findByPk(new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1))->getJson_certificados(true);
        $this->assertCount(1, $certs);
    }

    public function test_borrar_certificado_devuelve_la_nota_a_pendientes()
    {
        $nota = (new PersonaNotaFactory())->createSimple(new PersonaNotaPk(self::ID_NOM, self::ID_NIVEL, 1));
        $this->repoDl->Guardar($nota);
        $this->repoOtra->addCertificado(self::ID_NOM, 'TEST 2/26', null);
        $clave = self::ID_NOM . ':' . self::ID_NIVEL;
        $this->assertNotContains($clave, $this->idsPendientes());

        $this->repoOtra->deleteCertificado('TEST 2/26');

        $this->assertContains($clave, $this->idsPendientes());
    }
}
