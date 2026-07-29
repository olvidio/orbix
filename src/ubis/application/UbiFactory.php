<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\ubis\domain\entity\CentroEx;

final class UbiFactory
{
    public function __construct(
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private CentroEllosRepositoryInterface $centroEllosRepository,
    ) {
    }

    public function newUbi(int|string $id_ubi): Casa|Centro|CentroDl|CentroEx|CentroEllas|CentroEllos|null
    {
        // En DMZ (SV) no hay oDB/oDBP/oDBR: centros solo vía comun (ellos/ellas).
        if (ConfigGlobal::is_dmz()) {
            $centroRepository = $this->centroEllosRepository;
            if ((int)substr((string)$id_ubi, 0, 1) === 2) {
                $centroRepository = $this->centroEllasRepository;
            }
            return $centroRepository->findById((int)$id_ubi);
        }

        if ((int)substr((string)$id_ubi, 0, 1) === 2) {
            if (ConfigGlobal::mi_sfsv() === 1) {
                $centroRepository = $this->centroEllasRepository;
            } else {
                $centroRepository = DependencyResolver::get(CentroRepositoryInterface::class);
            }
        } else {
            if (ConfigGlobal::mi_sfsv() === 2) {
                $centroRepository = $this->centroEllosRepository;
            } else {
                $centroRepository = DependencyResolver::get(CentroRepositoryInterface::class);
            }
        }

        $oCentro = $centroRepository->findById((int)$id_ubi);
        if ($oCentro !== null) {
            $tipo_ubi = $oCentro->getTipo_ubi();
            switch ($tipo_ubi) {
                case 'ctrdl':
                case 'ctrsf':
                    if ($oCentro->getDl() === ConfigGlobal::mi_delef()) {
                        $oCentro = DependencyResolver::get(CentroDlRepositoryInterface::class)->findById((int)$id_ubi);
                    }
                    break;
                case 'ctrex':
                    $oCentro = DependencyResolver::get(CentroExRepositoryInterface::class)->findById((int)$id_ubi);
                    break;
                default:
                    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                    exit($err_switch);
            }

            return $oCentro;
        }

        return DependencyResolver::get(CasaRepositoryInterface::class)->findById((int)$id_ubi);
    }
}
