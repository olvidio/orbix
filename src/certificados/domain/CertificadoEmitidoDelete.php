<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\certificados\application\support\CertificadosSession;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;

class CertificadoEmitidoDelete
{
    private PDO $oDbl;

    public function __construct(
        private readonly ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
    ) {
        $this->oDbl = GlobalPdo::get('oDB');
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
        $error_txt = '';
        if ($Qid_item <= 0) {
            return _("No se encuentra el certificado");
        }

        $certificadoEmitidoRepository = $this->certificadoEmitidoRepository();
        $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
        if ($oCertificadoEmitido === null) {
            return _("No se encuentra el certificado");
        }

        $certificado = $oCertificadoEmitido->getCertificado();
        if ($certificadoEmitidoRepository->Eliminar($oCertificadoEmitido) === false) {
            $error_txt .= $certificadoEmitidoRepository->getErrorTxt();
        }

        $esquema_region_stgr = CertificadosSession::esquemaRegionStgr();
        /** @var PersonaNotaOtraRegionStgrRepositoryInterface $personaNotaOtraRepo */
        $personaNotaOtraRepo = DependencyResolver::make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr],
        );
        if ($certificado !== null && $certificado !== '') {
            $personaNotaOtraRepo->deleteCertificado($certificado);
        }

        return $error_txt;
    }

    private function certificadoEmitidoRepository(): CertificadoEmitidoRepositoryInterface
    {
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
