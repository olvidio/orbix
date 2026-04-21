<?php

namespace src\misas\application;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

class ZonaSacdDatosGet
{
    /**
     * @return array{error: string, payload: array<string, mixed>}
     */
    public static function execute(int $id_zona, int $id_sacd): array
    {
        $error_txt = '';
        $payload = [];

        $aWhere = ['id_zona' => $id_zona, 'id_nom' => $id_sacd];
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $cZonaSacd = $ZonaSacdRepository->getZonasSacds($aWhere);
        if (empty($cZonaSacd)) {
            $error_txt = _('No existe');
        } else {
            $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
            $oPersona = $PersonaSacdRepository->findById($id_sacd);
            $nom = ($oPersona === null) ? '' : (string)$oPersona->getNombreApellidos();
            $payload['nombre_sacd'] = ($nom === '') ? '?' : $nom;

            $oZonaSacd = $cZonaSacd[0];
            $payload['dw1'] = $oZonaSacd->isDw1();
            $payload['dw2'] = $oZonaSacd->isDw2();
            $payload['dw3'] = $oZonaSacd->isDw3();
            $payload['dw4'] = $oZonaSacd->isDw4();
            $payload['dw5'] = $oZonaSacd->isDw5();
            $payload['dw6'] = $oZonaSacd->isDw6();
            $payload['dw7'] = $oZonaSacd->isDw7();
        }

        return ['error' => $error_txt, 'payload' => $payload];
    }
}
