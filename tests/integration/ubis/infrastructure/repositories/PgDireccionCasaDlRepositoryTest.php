<?php

namespace Tests\integration\ubis\infrastructure\persistence\postgresql;

use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use Tests\factories\ubis\DireccionFactory;
use Tests\myTest;

class PgDireccionCasaDlRepositoryTest extends myTest
{
    private DireccionCasaDlRepositoryInterface $repository;
    private DireccionFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(DireccionCasaDlRepositoryInterface::class);
        $this->factory = new DireccionFactory();
    }

    public function test_guardar_eliminar_direccion()
    {
        $o = $this->factory->createSimple();
        $id = $o->getId_direccion();
        $this->assertTrue($this->repository->Guardar($o));

        $oGuardado = $this->repository->findById($id);
        $this->assertNotNull($oGuardado);
        $this->assertInstanceOf(Direccion::class, $oGuardado);

        $this->assertTrue($this->repository->Eliminar($oGuardado));
        $this->assertNull($this->repository->findById($id));
    }

    /**
     * El plano de una dirección es un fichero cualquiera (una foto, un jpg, un pdf…): contenido binario
     * de cualquier tamaño. Una vez subido, la dirección se tiene que poder leer y volver a guardar sin
     * que el plano cambie ni un byte.
     */
    public function test_el_plano_es_un_fichero_binario_y_se_conserva_byte_a_byte(): void
    {
        // Cabecera y final de un JPEG con relleno arbitrario; acaba en salto de línea y lleva bytes nulos.
        $plano = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00" . random_bytes(3000) . "\xFF\xD9\n";
        $o = $this->factory->createSimple();
        $id = $o->getId_direccion();
        $this->assertTrue($this->repository->Guardar($o));
        try {
            $this->repository->planoUpload($id, 'plano_casa', 'jpg', $plano);

            $leida = $this->repository->findById($id);
            $this->assertInstanceOf(Direccion::class, $leida, 'la dirección con plano se puede leer');
            $this->assertSame(md5($plano), md5((string) $leida->getPlano_doc()), 'la entidad lleva el plano tal cual');
            $this->assertSame('jpg', $leida->getPlano_extension());

            // Modificar otro dato de la dirección no toca el plano.
            $leida->setPoblacion('test_poblacion_con_plano');
            $this->assertTrue($this->repository->Guardar($leida));
            $descarga = $this->repository->planoDownload($id);
            $contenido = is_resource($descarga['plano_doc']) ? stream_get_contents($descarga['plano_doc']) : $descarga['plano_doc'];
            $this->assertSame(strlen($plano), strlen((string) $contenido));
            $this->assertSame(md5($plano), md5((string) $contenido), 'el plano guardado es el que se subió');
        } finally {
            $this->repository->planoBorrar($id);
            $resto = $this->repository->findById($id);
            if ($resto !== null) {
                $this->repository->Eliminar($resto);
            }
        }
        $this->assertNull($this->repository->findById($id));
    }

    public function test_find_by_id_no_existente()
    {
        $this->assertNull($this->repository->findById(999999981));
    }
}
