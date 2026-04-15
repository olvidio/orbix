<?php

namespace src\zonassacd\application;

use core\ConfigGlobal;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\entity\ZonaSacd;
use function core\is_true;

class ZonaSacdAjax
{
    public static function execute(string $que, string $id_zona, string $id_zona_new, int $acumular, array $sel): array
    {
        return match ($que) {
            'get_lista' => self::getLista($id_zona),
            'get_lista_tot' => self::getListaTot(),
            'update' => self::update($id_zona, $id_zona_new, $acumular, $sel),
            default => ['error' => sprintf(_("opción no definida en switch: %s"), $que)],
        };
    }

    private static function getLista(string $id_zona): array
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

    private static function getListaTot(): array
    {
        $mi_dl = ConfigGlobal::mi_delef();
        $aWhere = ['sacd' => 't', 'dl' => $mi_dl, '_ordre' => 'apellido1,apellido2,nom'];
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $cSacds = $PersonaSacdRepository->getPersonas($aWhere);
        $a_valores = [];
        $i = 0;
        foreach ($cSacds as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom]);
            $a_zonas = [];
            foreach ($cZonaSacd as $oZonaSacd) {
                $id_zona = $oZonaSacd->getId_zona();
                $propia = $oZonaSacd->isPropia();
                $oZona = $ZonaRepository->findById($id_zona);
                $orden = $propia === true ? 0 : $oZona->getOrden();
                $a_zonas[$orden] = [$oZona->getNombre_zona(), $propia];
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

    private static function update(string $id_zona, string $id_zona_new, int $acumular, array $sel): array
    {
        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $errores = [];
        if (empty($id_zona_new)) {
            return ['tipo' => 'update', 'mensaje' => '', 'error' => ''];
        }
        $nuevaZona = $id_zona_new === 'no' ? '' : $id_zona_new;
        foreach ($sel as $id_nom) {
            if ($acumular === 2) {
                if (empty($nuevaZona)) {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $cZonaSacd[0]->DBEliminar() === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $nuevaZona]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setPropia('f');
                    } else {
                        $oZonaSacd = new ZonaSacd();
                        $oZonaSacd->setId_item($ZonaSacdRepository->getNewId());
                        $oZonaSacd->setId_nom($id_nom);
                        $oZonaSacd->setId_zona($nuevaZona);
                        $oZonaSacd->setPropia('f');
                    }
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                }
            } else {
                if ($id_zona === 'no' || $id_zona == 0) {
                    $oZonaSacd = new ZonaSacd();
                    $oZonaSacd->setId_item($ZonaSacdRepository->getNewId());
                    $oZonaSacd->setId_nom($id_nom);
                    $oZonaSacd->setId_zona($nuevaZona);
                    $oZonaSacd->setPropia('t');
                    if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                        $errores[] = _("hay un error, no se ha guardado");
                    }
                } elseif (empty($nuevaZona)) {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd) && $cZonaSacd[0]->DBEliminar() === false) {
                        $errores[] = _("hay un error, no se ha eliminado");
                    }
                } else {
                    $cZonaSacd = $ZonaSacdRepository->getZonasSacds(['id_nom' => $id_nom, 'id_zona' => $id_zona]);
                    if (!empty($cZonaSacd)) {
                        $oZonaSacd = $cZonaSacd[0];
                        $oZonaSacd->setId_zona($nuevaZona);
                        $oZonaSacd->setPropia('t');
                        if ($ZonaSacdRepository->Guardar($oZonaSacd) === false) {
                            $errores[] = _("hay un error, no se ha guardado");
                        }
                    }
                }
            }
        }
        return ['tipo' => 'update', 'mensaje' => implode("\n", $errores), 'error' => ''];
    }
}
