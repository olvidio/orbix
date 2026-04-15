<?php

namespace src\zonassacd\application;

use core\ConfigGlobal;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use function core\is_true;

class ZonaSacdLista
{
    public static function execute(string $id_zona): array
    {
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $a_valores = [];
        if ($id_zona === 'no') {
            $mi_dl = ConfigGlobal::mi_delef();
            $aWhere = [
                'id_tabla' => "'n','a','sssc','pa','pn'",
                'sacd' => 't',
                'situacion' => 'A',
                'dl' => $mi_dl,
                '_ordre' => 'apellido1,apellido2,nom',
            ];
            $aOperador = ['id_tabla' => 'IN'];
            $cSacds = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
            $i = 0;
            foreach ($cSacds as $oPersona) {
                $id_nom = $oPersona->getId_nom();
                $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom]);
                if (is_array($cZonaSacd) && count($cZonaSacd) < 1) {
                    $a_valores[$i]['sel'] = $id_nom;
                    $a_valores[$i][1] = $oPersona->getPrefApellidosNombre();
                    $a_valores[$i][2] = $oPersona->getId_tabla();
                    $i++;
                }
            }
        } else {
            $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
            $oZona = $ZonaRepository->findById($id_zona);
            $nombre_zona = $oZona->getNombre_zona();
            $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_zona' => $id_zona], []);
            $i = 0;
            $aAp1 = [];
            foreach ($cZonaSacd as $oZonaSacd) {
                $id_nom = $oZonaSacd->getId_nom();
                $oPersona = Persona::findPersonaEnGlobal($id_nom);
                $ap_nom = $oPersona === null ? sprintf(_("No encuentro e nadie con id_nom %s"), $id_nom) : $oPersona->getPrefApellidosNombre();
                $aAp1[$i] = $ap_nom;
                $a_valores[$i]['sel'] = $id_nom;
                $a_valores[$i][1] = $ap_nom;
                $a_valores[$i][2] = $nombre_zona;
                $a_valores[$i][3] = $oZonaSacd->isPropia();
                $a_valores[$i][4] = is_true($oZonaSacd->isDw1()) ? 'x' : '-';
                $a_valores[$i][5] = is_true($oZonaSacd->isDw2()) ? 'x' : '-';
                $a_valores[$i][6] = is_true($oZonaSacd->isDw3()) ? 'x' : '-';
                $a_valores[$i][7] = is_true($oZonaSacd->isDw4()) ? 'x' : '-';
                $a_valores[$i][8] = is_true($oZonaSacd->isDw5()) ? 'x' : '-';
                $a_valores[$i][9] = is_true($oZonaSacd->isDw6()) ? 'x' : '-';
                $a_valores[$i][10] = is_true($oZonaSacd->isDw7()) ? 'x' : '-';
                $i++;
            }
            if (!empty($a_valores)) {
                array_multisort($aAp1, SORT_ASC, SORT_STRING, $a_valores);
            }
        }

        return [
            'tipo' => 'tabla',
            'id_tabla' => 'zona_sacd_ajax',
            'a_cabeceras' => [_("sacd"), _("zona"), _("propia"), _("L"), _("M"), _("X"), _("J"), _("V"), _("S"), _("D")],
            'a_botones' => [['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"]],
            'a_valores' => $a_valores,
            'error' => '',
        ];
    }
}
