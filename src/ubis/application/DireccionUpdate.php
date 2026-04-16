<?php

namespace src\ubis\application;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Direccion;
use function core\is_true;

final class DireccionUpdate
{
    /**
     * @param array<string, mixed> $input
     */
    public static function execute(array $input): string
    {
        $Qobj_dir = (string)($input['obj_dir'] ?? '');
        $Qid_ubi = (int)($input['id_ubi'] ?? 0);
        $Qidx = (string)($input['idx'] ?? '');

        try {
            $DireccionRepository = DireccionesResolver::direccionRepo($Qobj_dir);
            $UbiRepository = DireccionesResolver::ubiRepo($Qobj_dir);
        } catch (\InvalidArgumentException $e) {
            return $e->getMessage();
        }

        $oUbi = $UbiRepository->findById($Qid_ubi);
        if ($oUbi === null) {
            return _('no se encuentra el ubi');
        }

        if ($Qidx === 'nuevo') {
            $id_direccion = $DireccionRepository->getNewId();
            $oDireccion = new Direccion();
            $oDireccion->setId_direccion($id_direccion);
        } else {
            $id_direccion_csv = (string)($input['id_direccion'] ?? '');
            $a_id_direccion = explode(',', $id_direccion_csv);
            $id_direccion = (int)($a_id_direccion[$Qidx] ?? 0);
            $oDireccion = $DireccionRepository->findById($id_direccion);
            if ($oDireccion === null) {
                return _('no se encuentra la dirección');
            }
        }

        $Qnom_sede = (string)($input['nom_sede'] ?? '');
        $Qdireccion = (string)($input['direccion'] ?? '');
        $Qa_p = (string)($input['a_p'] ?? '');
        $Qc_p = (string)($input['c_p'] ?? '');
        $Qpoblacion = (string)($input['poblacion'] ?? '');
        $Qprovincia = (string)($input['provincia'] ?? '');
        $Qpais = (string)($input['pais'] ?? '');
        $Qobserv = (string)($input['observ'] ?? '');
        $Qf_direccion = (string)($input['f_direccion'] ?? '');
        $Qlatitud = (string)($input['latitud'] ?? '');
        $Qlongitud = (string)($input['longitud'] ?? '');

        $oF_direccion = $Qf_direccion === '' ? null : DateTimeLocal::createFromLocal($Qf_direccion);
        $cp_dcha = is_true((string)($input['cp_dcha'] ?? ''));
        $propietario = is_true((string)($input['propietario'] ?? ''));
        $principal = is_true((string)($input['principal'] ?? ''));

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

        $oUbi->cambiarEstadoPropietario($id_direccion, $propietario);
        if ($principal) {
            $oUbi->establecerDireccionPrincipal($id_direccion);
        }

        return '';
    }
}
