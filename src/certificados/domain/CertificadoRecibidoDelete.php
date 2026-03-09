<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;

class CertificadoRecibidoDelete
{
    private PDO $oDbl;

    /**
     * Para poder cambiar la conexión en el caso de los tests.
     *
     * @param $oDbl
     * @return void
     */
    public function setoDbl(PDO $oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    /**
     * @param int $Qid_item
     * @return string
     */
    public function delete(int $Qid_item): string
    {
        $error_txt = '';
        if (!empty($Qid_item)) {
            $certificadoRecibidoRepository = $this->certificadoRecibidoRepository();
            $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
            if ($certificadoRecibidoRepository->Eliminar($oCertificadoRecibido) === false) {
                $error_txt .= $certificadoRecibidoRepository->getErrorTxt();
            }
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }

    private function certificadoRecibidoRepository(): CertificadoRecibidoRepositoryInterface
    {
        if (!isset($this->oDbl)) {
            return $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
        }

        $factory = $GLOBALS['container']->get(ConnectionRepositoryFactoryInterface::class);
        return $factory->createWithConnection(CertificadoRecibidoRepositoryInterface::class, $this->oDbl);
    }
}
