<?php

namespace Tests\unit\certificados;

use certificados\domain\CertificadoUpload;
use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use Faker\Factory;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use Tests\factories\certificados\CertificadosFactory;
use Tests\myTest;

class CertificadoUploadTest extends myTest
{
    private int $id_nom;
    private int $id_schema_persona;
    private $snew_esquema;
    private $sreg_dl_dst;
    private array $cCertificados;
    private mixed $session_org;

    public function __construct(string $name)
    {
        $this->generarCertificados('H-H');

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

    ////////////////////////////////////////////////////////////////////////////

    public function test_subir_texto_certificados()
    {
        $faker = Factory::create();
        $oDBdst = $this->setConexion('H-Hv');
        foreach ($this->cCertificados as $Certificado) {
            $CertificadoUpload = new CertificadoUpload();

            // guardar uno nuevo
            $contenido_doc = $Certificado->getDocumento();
            $id_nom = $Certificado->getId_nom();
            $certificado = $Certificado->getCertificado();
            $firmado = $Certificado->isFirmado();
            $idioma = $Certificado->getIdioma();
            $oF_certificado = $Certificado->getF_certificado();
            $oF_enviado = $Certificado->getF_enviado();
            $destino = $Certificado->getDestino();
            $CertificadoUpload::setoDbl($oDBdst);
            $CertificadoDB = $CertificadoUpload::uploadNew($id_nom,$contenido_doc, $idioma,  $certificado, $firmado, $oF_certificado, $oF_enviado, $destino);

            // cambiar le texto
            $contenido_doc_new = $faker->sentence(450);
            $CertificadoUpload::setoDbl($oDBdst);
            $CertificadoDB2 = $CertificadoUpload::uploadTxtFirmado($CertificadoDB->getId_item(), $contenido_doc_new);

            $this->assertInstanceOf(Certificado::class, $CertificadoDB2);

            $id_item = $CertificadoDB2->getId_item();
            $CertificadoRepository = new CertificadoRepository();
            $CertificadoRepository->setoDbl($oDBdst);
            $CertificadoDB3 = $CertificadoRepository->findById($id_item);

            // En el certificado subido no existe la fecha de envío,
            // por tanto no se puede comparar el objeto completo
            $this->assertEquals($CertificadoDB3->getDocumento(), $CertificadoDB2->getDocumento());

            $this->assertEquals($CertificadoDB3->getCertificado(), $Certificado->getCertificado());
            $this->assertEquals($CertificadoDB3->getF_certificado(), $Certificado->getF_certificado());
            $this->assertEquals($CertificadoDB3->getDestino(), $Certificado->getDestino());
            $this->assertEquals($CertificadoDB3->getId_nom(), $Certificado->getId_nom());
            $this->assertEquals($CertificadoDB3->getIdioma(), $Certificado->getIdioma());
            $this->assertEquals($CertificadoDB3->isFirmado(), $Certificado->isFirmado());
        }
    }

    public function test_subir_certificados()
    {
        $oDBdst = $this->setConexion('H-Hv');
        foreach ($this->cCertificados as $Certificado) {
            $CertificadoUpload = new CertificadoUpload();

            $contenido_doc = $Certificado->getDocumento();
            $id_nom = $Certificado->getId_nom();
            $certificado = $Certificado->getCertificado();
            $firmado = $Certificado->isFirmado();
            $idioma = $Certificado->getIdioma();
            $oF_certificado = $Certificado->getF_certificado();
            $oF_enviado = $Certificado->getF_enviado();
            $destino = $Certificado->getDestino();

            $CertificadoUpload::setoDbl($oDBdst);
            $CertificadoDB = $CertificadoUpload::uploadNew($id_nom,$contenido_doc, $idioma,  $certificado, $firmado, $oF_certificado, $oF_enviado, $destino);

            $this->assertInstanceOf(Certificado::class, $CertificadoDB);

            $id_item = $CertificadoDB->getId_item();
            $CertificadoRepository = new CertificadoRepository();
            $CertificadoRepository->setoDbl($oDBdst);
            $CertificadoDB2 = $CertificadoRepository->findById($id_item);

            // En el certificado subido no existe la fecha de envío,
            // por tanto no se puede comparar el objeto completo
            $this->assertEquals($CertificadoDB->getCertificado(), $CertificadoDB2->getCertificado());
            $this->assertEquals($CertificadoDB->getF_certificado(), $CertificadoDB2->getF_certificado());
            $this->assertEquals($CertificadoDB->getDestino(), $CertificadoDB2->getDestino());
            $this->assertEquals($CertificadoDB->getDocumento(), $CertificadoDB2->getDocumento());
            $this->assertEquals($CertificadoDB->getId_nom(), $CertificadoDB2->getId_nom());
            $this->assertEquals($CertificadoDB->getIdioma(), $CertificadoDB2->getIdioma());
            $this->assertEquals($CertificadoDB->isFirmado(), $CertificadoDB2->isFirmado());
        }
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

    public function generarCertificados(string $esquema): void
    {
        //  Generar certificados para id_nom (debe existir, para que no dé error)!!!
        switch ($esquema) {
            case 'H-H':
                $this->id_nom = 100111832;
                $this->id_schema_persona = 1001;
                break;
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
        $certificadosFactory = new CertificadosFactory();
        $certificadosFactory->setCount(10);

        $a_reg = explode('-', $esquema);
        $region = $a_reg[0];

        $this->cCertificados = $certificadosFactory->create($this->id_nom, $region);
    }

    private function conexionDst($exterior = FALSE): \PDO
    {
        $this->snew_esquema = $this->sreg_dl_dst;
        return $this->setConexion($this->snew_esquema, $exterior);
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
