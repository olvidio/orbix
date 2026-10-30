<?php

namespace src\misas\application;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaSacdField;

class ZonaSacdDatosGet
{

    public function __construct(
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }
    /**
     * @return array{error: string, payload: array<string, mixed>}
     */
    public function execute(int $id_zona, int $id_sacd): array
    {
        $error_txt = '';
        $payload = [];

        $cZonaSacd = $this->zonaSacdRepository->matching(
            Criteria::create()
                ->where(Filter::eq(ZonaSacdField::IdZona, $id_zona))
                ->andWhere(Filter::eq(ZonaSacdField::IdNom, $id_sacd))
        );
        if (empty($cZonaSacd)) {
            $error_txt = _("No existe");
        } else {
            $oPersona = $this->personaSacdRepository->findById($id_sacd);
            $nom = ($oPersona === null) ? '' : (string)$oPersona->getNombreApellidos();
            $payload['nombre_sacd'] = ($nom === '') ? '?' : $nom;

            $oZonaSacd = $cZonaSacd[0];
            $payload['propia'] = $oZonaSacd->isPropia();
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
