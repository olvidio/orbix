<?php

namespace Tests\unit\certificados;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use RuntimeException;
use src\certificados\domain\CertificadoEmitidoDelete;
use src\certificados\domain\CertificadoEmitidoUpload;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\shared\infrastructure\DependencyResolver;
use Tests\factories\certificados\CertificadosFactory;
use Tests\myTest;

class CertificadoEmitidoDeleteTest extends myTest
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
        $_SESSION['session_auth']['esquema'] = 'H-Hv';
    }

    ////////////////////////////////////////////////////////////////////////////


    public function test_delete_certificados()
    {
        $oDBdst = $this->setConexion('H-Hv');
        foreach ($this->cCertificados as $Certificado) {
            $CertificadoUpload = DependencyResolver::get(CertificadoEmitidoUpload::class);
            $CertificadoUpload->setoDbl($oDBdst);
            $CertificadoDelete = DependencyResolver::get(CertificadoEmitidoDelete::class);
            $CertificadoDelete->setoDbl($oDBdst);

            $contenido_doc = $Certificado->getDocumento();
            $id_nom = $Certificado->getId_nom();
            $certificado = $Certificado->getCertificado();
            $firmado = $Certificado->isFirmado();
            $idioma = (string) ($Certificado->getIdiomaVo()?->value() ?? '');
            $oF_certificado = $Certificado->getF_certificado();
            $oF_recibido = $Certificado->getF_enviado();
            $destino = $Certificado->getDestino();

            $CertificadoDB = $CertificadoUpload->uploadNew($id_nom, $contenido_doc, $idioma, $certificado, $firmado, $oF_certificado, $oF_recibido, $destino);

            $this->assertInstanceOf(CertificadoEmitido::class, $CertificadoDB);

            $id_item = $CertificadoDB->getId_item();

            $err_txt = $CertificadoDelete->delete($id_item);
            $this->assertEmpty($err_txt);

            $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
            $certificadoEmitidoRepository->setoDbl($oDBdst);
            $busqueda = $certificadoEmitidoRepository->findById($id_item);
            $this->assertNull($busqueda, "El certificado con ID $id_item debería haber sido eliminado.");
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
            case 'Galbel-crGalbel':
                $this->id_nom = 103612;
                $this->id_schema_persona = 1036;
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
