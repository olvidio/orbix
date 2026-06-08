<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

class CertificadoEmitidoUpload
{
    private ?PDO $oDbl = null;

    public function __construct(
        private readonly CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        private readonly ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
    ) {
    }

    /**
     * Para poder cambiar la conexión en el caso de los tests.
     */
    public function setoDbl(PDO $oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    public function uploadTxtFirmado(int $id_item, string $contenido_doc): string|CertificadoEmitido
    {
        $certificadoEmitidoRepository = $this->certificadoEmitidoRepository();
        $oCertificadoEmitido = $certificadoEmitidoRepository->findById($id_item);
        if ($oCertificadoEmitido === null) {
            return _("No se encuentra el certificado");
        }

        $oCertificadoEmitido->setDocumento($contenido_doc);
        $oCertificadoEmitido->setFirmado(true);

        if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
            return $certificadoEmitidoRepository->getErrorTxt();
        }

        return $oCertificadoEmitido;
    }

    public function uploadNew(
        int $id_nom,
        false|string $contenido_doc,
        string $idioma,
        string $certificado,
        bool $firmado,
        DateTimeLocal|null $oF_certificado,
        DateTimeLocal|null $oF_enviado,
        ?string $destino,
    ): string|CertificadoEmitido {
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if ($oPersona === null) {
            return sprintf(_('No se encuentra la persona con id_nom: %d'), $id_nom);
        }

        $nom = $oPersona->getApellidosNombre();
        if ($destino === null || $destino === '') {
            $destino = $oPersona->getDlVo()?->value() ?? '';
        }

        $certificadoEmitidoRepository = $this->certificadoEmitidoRepository();
        $id_item = $certificadoEmitidoRepository->getNewId_item();
        $oCertificadoEmitido = new CertificadoEmitido();
        $oCertificadoEmitido->setId_item((int) $id_item);
        $oCertificadoEmitido->setDocumento(is_string($contenido_doc) ? $contenido_doc : null);
        $oCertificadoEmitido->setId_nom($id_nom);
        $oCertificadoEmitido->setNom($nom);
        $oCertificadoEmitido->setDestino($destino);
        $oCertificadoEmitido->setIdiomaVo($idioma);
        $oCertificadoEmitido->setCertificado($certificado);
        $oCertificadoEmitido->setFirmado($firmado);
        $oCertificadoEmitido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoEmitido->setF_certificado($oF_certificado);
        $oCertificadoEmitido->setF_enviado($oF_enviado);

        if ($certificadoEmitidoRepository->Guardar($oCertificadoEmitido) === false) {
            return $certificadoEmitidoRepository->getErrorTxt();
        }

        return $oCertificadoEmitido;
    }

    private function certificadoEmitidoRepository(): CertificadoEmitidoRepositoryInterface
    {
        if ($this->oDbl === null) {
            return $this->certificadoEmitidoRepository;
        }

        $repo = $this->connectionRepositoryFactory->createWithConnection(
            CertificadoEmitidoRepositoryInterface::class,
            $this->oDbl,
        );
        if (!$repo instanceof CertificadoEmitidoRepositoryInterface) {
            throw new \RuntimeException('Repositorio de certificados emitidos inválido');
        }

        return $repo;
    }
}
