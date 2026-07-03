<?php

namespace src\ubis\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use src\shared\domain\helpers\FuncTablasSupport;
final class DireccionUpdate
{
    public function __construct(
        private DireccionesResolver $direccionesResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qobj_dir = FuncTablasSupport::inputString($input, 'obj_dir');
        $Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        $Qidx = FuncTablasSupport::inputString($input, 'idx');

        try {
            $DireccionRepository = $this->direccionesResolver->direccionRepo($Qobj_dir);
            $UbiRepository = $this->direccionesResolver->ubiRepo($Qobj_dir);
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }

        $oUbi = $UbiRepository->findById($Qid_ubi);
        if ($oUbi === null) {
            return _('no se encuentra el ubi');
        }

        if ($Qidx === 'nuevo') {
            if (
                !$DireccionRepository instanceof DireccionCentroDlRepositoryInterface
                && !$DireccionRepository instanceof DireccionCentroExRepositoryInterface
                && !$DireccionRepository instanceof DireccionCasaDlRepositoryInterface
                && !$DireccionRepository instanceof DireccionCasaExRepositoryInterface
            ) {
                return _('operación no soportada para este tipo de dirección');
            }
            $id_auto = $DireccionRepository->getNewId();
            $id_direccion = $DireccionRepository->getNewIdDireccion($id_auto);
            $oDireccion = new Direccion();
            $oDireccion->setId_direccion($id_direccion);
        } else {
            $id_direccion_csv = FuncTablasSupport::inputString($input, 'id_direccion');
            $a_id_direccion = explode(',', $id_direccion_csv);
            $id_direccion = 0;
            if ($Qidx !== '' && ctype_digit($Qidx)) {
                $idx = (int) $Qidx;
                if (isset($a_id_direccion[$idx])) {
                    $id_direccion = (int) $a_id_direccion[$idx];
                }
            }
            $oDireccion = $DireccionRepository->findById($id_direccion);
            if ($oDireccion === null) {
                return _('no se encuentra la dirección');
            }
        }

        $Qnom_sede = FuncTablasSupport::inputString($input, 'nom_sede');
        $Qdireccion = FuncTablasSupport::inputString($input, 'direccion');
        $Qa_p = FuncTablasSupport::inputString($input, 'a_p');
        $Qc_p = FuncTablasSupport::inputString($input, 'c_p');
        $Qpoblacion = FuncTablasSupport::inputString($input, 'poblacion');
        $Qprovincia = FuncTablasSupport::inputString($input, 'provincia');
        $Qpais = FuncTablasSupport::inputString($input, 'pais');
        $Qobserv = FuncTablasSupport::inputString($input, 'observ');
        $Qf_direccion = FuncTablasSupport::inputString($input, 'f_direccion');
        $Qlatitud = FuncTablasSupport::inputString($input, 'latitud');
        $Qlongitud = FuncTablasSupport::inputString($input, 'longitud');

        $rawF_direccion = $Qf_direccion === '' ? null : DateTimeLocal::createFromLocal($Qf_direccion);
        $oF_direccion = $rawF_direccion instanceof DateTimeLocal ? $rawF_direccion : null;
        $cp_dcha = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($input, 'cp_dcha'));
        $propietario = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($input, 'propietario'));
        $principal = FuncTablasSupport::isTrue(FuncTablasSupport::inputString($input, 'principal'));

        $oDireccion->setNom_sede($Qnom_sede);
        $oDireccion->setDireccion($Qdireccion);
        $oDireccion->setA_p($Qa_p);
        $oDireccion->setC_p($Qc_p);
        $oDireccion->setPoblacion($Qpoblacion);
        $oDireccion->setProvincia($Qprovincia);
        $oDireccion->setPais($Qpais);
        $oDireccion->setObserv($Qobserv);
        $oDireccion->setF_direccion($oF_direccion);
        $oDireccion->setCp_dcha($cp_dcha);
        $oDireccion->setLatitud((float)$Qlatitud);
        $oDireccion->setLongitud((float)$Qlongitud);

        $DireccionRepository->Guardar($oDireccion);

        if ($Qidx === 'nuevo') {
            $oUbi->addDireccion($id_direccion, $principal ?? false, $propietario ?? false);
        }
        $oUbi->cambiarEstadoPropietario($id_direccion, $propietario ?? false);
        if ($principal) {
            $oUbi->establecerDireccionPrincipal($id_direccion);
        }

        return '';
    }
}
