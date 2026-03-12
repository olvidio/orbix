<?php

namespace src\certificados\domain;

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;

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
            $certificadoEmitidoRepository = $this->certificadoEmitidoRepository();
            $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
            $certificado = $oCertificadoEmitido->getCertificado();
            if ($certificadoEmitidoRepository->Eliminar($oCertificadoEmitido) === false) {
                $error_txt .= $certificadoEmitidoRepository->getErrorTxt();
            }
            // Hay que borrar también el certificado de las notas_otra_region_stgr
            // Se supone que si accedo a esta página es porque soy una región del stgr.
            $esquema_region_stgr = $_SESSION['session_auth']['esquema'];
            $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
            $PersonaNotaOtraRegionStgrRepository = $this->bindConnection($PersonaNotaOtraRegionStgrRepository);

            $PersonaNotaOtraRegionStgrRepository->deleteCertificado($certificado);
        } else {
            $error_txt = _("No se encuentra el certificado");
        }
        return $error_txt;
    }

    private function certificadoEmitidoRepository(): CertificadoEmitidoRepositoryInterface
    {
        $factory = $GLOBALS['container']->get(ConnectionRepositoryFactoryInterface::class);
        return $factory->createWithConnection(CertificadoEmitidoRepositoryInterface::class, $this->oDbl);
    }

    private function bindConnection(object $object): object
    {
        $binder = $GLOBALS['container']->get(ConnectionObjectBinderInterface::class);
        return $binder->bindConnection($object, $this->oDbl);
    }
}
