<?php

namespace src\zonassacd\application;

use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaSacdField;

final class ZonaSacdListaTot
{
    public function __construct(
        private PersonaSacdRepositoryInterface $personaSacdRepository,
        private ZonaSacdRepositoryInterface $zonaSacdRepository,
        private ZonaRepositoryInterface $zonaRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $mi_dl = ConfigGlobal::mi_delef();
        $cSacds = $this->personaSacdRepository->matching(
            Criteria::create()
                ->where(Filter::eq(PersonaField::Sacd, true))
                ->andWhere(Filter::eq(PersonaField::Dl, $mi_dl))
                ->orderBy(PersonaField::Apellido1)
                ->addOrderBy(PersonaField::Apellido2)
                ->addOrderBy(PersonaField::Nom)
        );
        $a_valores = [];
        $i = 0;
        foreach ($cSacds as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $cZonaSacd = $this->zonaSacdRepository->matching(
                Criteria::create()->where(Filter::eq(ZonaSacdField::IdNom, $id_nom))
            );
            $a_zonas = [];
            foreach ($cZonaSacd as $oZonaSacd) {
                $id_zona = $oZonaSacd->getId_zona();
                $propia = $oZonaSacd->isPropia();
                $oZona = $this->zonaRepository->findById($id_zona);
                $orden = $propia === true ? 0 : $oZona?->getOrden() ?? 0;
                $a_zonas[$orden] = [$oZona?->getNombre_zona() ?? '', $propia];
            }
            if (count($a_zonas) >= 1) {
                ksort($a_zonas);
                foreach ($a_zonas as $a_zona) {
                    $a_valores[$i][1] = $ap_nom;
                    $a_valores[$i][2] = $a_zona[0];
                    $a_valores[$i][3] = empty($a_zona[1]) ? _("no") : _("si");
                    $i++;
                }
            } else {
                $a_valores[$i][1] = $ap_nom;
                $a_valores[$i][2] = '';
                $a_valores[$i][3] = '';
            }
            $i++;
        }
        return [
            'tipo' => 'lista',
            'a_cabeceras' => [_("sacd"), _("zona"), _("propia")],
            'a_valores' => $a_valores,
            'error' => '',
        ];
    }
}
