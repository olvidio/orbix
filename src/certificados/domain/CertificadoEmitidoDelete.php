<?php

namespace src\certificados\domain;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;

class CertificadoEmitidoDelete
{
    private \PDO $oDbl;

    public function __construct()
    {
        $this->oDbl = $GLOBALS['oDB'];
    }

    /**
     * Para poder cambiar le conexión en el caso de los tests.
     *
     * @param $oDbl
     * @return void
     */
    public function setoDbl($oDbl): void
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
            $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
            $certificadoEmitidoRepository->setoDbl($this->oDbl);
            $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
            $certificado = $oCertificadoEmitido->getCertificado();
            if ($certificadoEmitidoRepository->Eliminar($oCertificadoEmitido) === false) {
                $error_txt .= $certificadoEmitidoRepository->getErrorTxt();
            }
            // Hay que borrar también el certificado de las notas_otra_region_stgr
            // Se supone que si accedo a esta página es porque soy una región del stgr.
            $esquema_region_stgr = $_SESSION['session_auth']['esquema'];
            $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
            $PersonaNotaOtraRegionStgrRepository->setoDbl($this->oDbl);
            $PersonaNotaOtraRegionStgrRepository->deleteCertificado($certificado);
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }
}