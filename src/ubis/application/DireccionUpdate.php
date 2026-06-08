<?php

namespace src\ubis\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Direccion;
use function src\shared\domain\helpers\is_true;

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
        $Qobj_dir = input_string($input, 'obj_dir');
        $Qid_ubi = input_int($input, 'id_ubi');
        $Qidx = input_string($input, 'idx');

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
            $id_auto = $DireccionRepository->getNewId();
            $id_direccion = $DireccionRepository->getNewIdDireccion($id_auto);
            $oDireccion = new Direccion();
            $oDireccion->setId_direccion($id_direccion);
        } else {
            $id_direccion_csv = input_string($input, 'id_direccion');
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

        $Qnom_sede = input_string($input, 'nom_sede');
        $Qdireccion = input_string($input, 'direccion');
        $Qa_p = input_string($input, 'a_p');
        $Qc_p = input_string($input, 'c_p');
        $Qpoblacion = input_string($input, 'poblacion');
        $Qprovincia = input_string($input, 'provincia');
        $Qpais = input_string($input, 'pais');
        $Qobserv = input_string($input, 'observ');
        $Qf_direccion = input_string($input, 'f_direccion');
        $Qlatitud = input_string($input, 'latitud');
        $Qlongitud = input_string($input, 'longitud');

        $rawF_direccion = $Qf_direccion === '' ? null : DateTimeLocal::createFromLocal($Qf_direccion);
        $oF_direccion = $rawF_direccion instanceof DateTimeLocal ? $rawF_direccion : null;
        $cp_dcha = is_true(input_string($input, 'cp_dcha'));
        $propietario = is_true(input_string($input, 'propietario'));
        $principal = is_true(input_string($input, 'principal'));

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
