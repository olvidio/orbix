<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\personas\domain\entity\Persona;
use src\shared\config\ConfigGlobal;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;

class CertificadoRecibidoUpload
{
    private ?PDO $oDbl = null;

    public function __construct(
        private readonly CertificadoRecibidoRepositoryInterface $certificadoRecibidoRepository,
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

    public function uploadNew(
        int $Qid_item,
        int $Qid_nom,
        false|string $contenido_doc,
        string $Qidioma,
        string $Qcertificado,
        bool $firmado,
        DateTimeLocal|null $oF_certificado,
        DateTimeLocal|null $oF_recibido,
        ?string $destino,
    ): string|CertificadoRecibido {
        $oPersona = Persona::findPersonaEnGlobal($Qid_nom);
        if ($oPersona === null) {
            return sprintf(_('No se encuentra la persona con id_nom: %d'), $Qid_nom);
        }

        $nom = $oPersona->getApellidosNombre();
        if ($destino === null || $destino === '') {
            $destino = $oPersona->getDlVo()?->value() ?? '';
        }

        $certificadoRecibidoRepository = $this->certificadoRecibidoRepository();
        if ($Qid_item <= 0) {
            $id_item = $certificadoRecibidoRepository->getNewId_item();
            $oCertificadoRecibido = new CertificadoRecibido();
            $oCertificadoRecibido->setId_item((int) $id_item);
        } else {
            $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
            if ($oCertificadoRecibido === null) {
                return _("No se encuentra el certificado");
            }
        }

        $oCertificadoRecibido->setDocumento(is_string($contenido_doc) ? $contenido_doc : null);
        $oCertificadoRecibido->setId_nom($Qid_nom);
        $oCertificadoRecibido->setNom($nom);
        $oCertificadoRecibido->setDestino($destino);
        $oCertificadoRecibido->setIdiomaVo($Qidioma);
        $oCertificadoRecibido->setCertificado($Qcertificado);
        $oCertificadoRecibido->setFirmado($firmado);
        $oCertificadoRecibido->setEsquema_emisor(ConfigGlobal::mi_region_dl());
        $oCertificadoRecibido->setF_certificado($oF_certificado);
        $oCertificadoRecibido->setF_recibido($oF_recibido);

        if ($certificadoRecibidoRepository->Guardar($oCertificadoRecibido) === false) {
            return $certificadoRecibidoRepository->getErrorTxt();
        }

        return $oCertificadoRecibido;
    }

    private function certificadoRecibidoRepository(): CertificadoRecibidoRepositoryInterface
    {
        if ($this->oDbl === null) {
            return $this->certificadoRecibidoRepository;
        }

        $repo = $this->connectionRepositoryFactory->createWithConnection(
            CertificadoRecibidoRepositoryInterface::class,
            $this->oDbl,
        );
        if (!$repo instanceof CertificadoRecibidoRepositoryInterface) {
            throw new \RuntimeException('Repositorio de certificados recibidos inválido');
        }

        return $repo;
    }
}
