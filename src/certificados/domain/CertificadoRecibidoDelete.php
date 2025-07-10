<?php

namespace src\certificados\domain;

use PDO;
use src\certificados\application\repositories\CertificadoRecibidoRepository;

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
            $certificadoRecibidoRepository = new CertificadoRecibidoRepository();
            if (isset($this->oDbl)) {
                $certificadoRecibidoRepository->setoDbl($this->oDbl);
            }
            $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
            if ($certificadoRecibidoRepository->Eliminar($oCertificadoRecibido) === FALSE) {
                $error_txt .= $certificadoRecibidoRepository->getErrorTxt();
            }
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }
}