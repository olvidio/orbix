<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;

class CertificadoRecibidoDelete
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

    public function delete(int $Qid_item): string
    {
        if ($Qid_item <= 0) {
            return _("No se encuentra el certificado");
        }

        $certificadoRecibidoRepository = $this->certificadoRecibidoRepository();
        $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
        if ($oCertificadoRecibido === null) {
            return _("No se encuentra el certificado");
        }

        if ($certificadoRecibidoRepository->Eliminar($oCertificadoRecibido) === false) {
            return $certificadoRecibidoRepository->getErrorTxt();
        }

        return '';
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
