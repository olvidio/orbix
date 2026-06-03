<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

class UbisListaData
{
    /**
     * @return array{a_cabeceras: list<string>, a_valores: list<array<string|int>>}
     */
    public static function execute(string $nombre_ubi): array
    {
        $miSfsv = ConfigGlobal::mi_sfsv();

        $aWhereCasa = [];
        $aWhereCtr = [];
        $aOperadorCasa = [];
        $aOperadorCtr = [];

        if ($nombre_ubi !== '') {
            $nom_ubi = str_replace('+', "\+", $nombre_ubi);
            $aWhereCasa['nombre_ubi'] = $nom_ubi;
            $aOperadorCasa['nombre_ubi'] = 'sin_acentos';
            $aWhereCtr['nombre_ubi'] = $nom_ubi;
            $aOperadorCtr['nombre_ubi'] = 'sin_acentos';
        }

        switch ($miSfsv) {
            case 1:
                if (!($_SESSION['oPerm']->have_perm_oficina('vcsd') || $_SESSION['oPerm']->have_perm_oficina('des'))) {
                    $aWhereCasa['sv'] = 't';
                }
                break;
            case 2:
                $aWhereCasa['sf'] = 't';
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                return [
                    'error' => $err_switch,
                    'a_cabeceras' => [],
                    'a_valores' => [],
                ];
        }

        $aWhereCasa['active'] = 't';
        $aWhereCtr['active'] = 't';
        $aWhereCtr['cdc'] = 't';

        $a_ubis = [];
        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $cCasas = $CasaRepository->getCasas($aWhereCasa, $aOperadorCasa);
        foreach ($cCasas as $oCasa) {
            $nom = $oCasa->getNombre_ubi();
            $a_ubis[$nom] = $oCasa;
        }

        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $cCtr = $CentroRepository->getCentros($aWhereCtr, $aOperadorCtr);
        foreach ($cCtr as $oCentro) {
            $nom = $oCentro->getNombre_ubi();
            $a_ubis[$nom] = $oCentro;
        }

        uksort($a_ubis, 'strnatcasecmp');

        $a_cabeceras = [
            ucfirst(_("nombre del centro")),
            _("tipo"),
            _("dl"),
            ucfirst(_("región")),
            ucfirst(_("dirección")),
            _("cp"),
            ucfirst(_("ciudad")),
        ];

        $a_valores = [];
        $i = 0;
        foreach ($a_ubis as $oUbi) {
            $i++;
            $id_ubi = $oUbi->getId_ubi();
            $nom_ubi = $oUbi->getNombre_ubi();
            $tipo_ubi = substr($oUbi->getTipo_ubi() ?? '', 0, 3);

            $row = [
                'sel' => $id_ubi,
                1 => $nom_ubi,
                2 => $tipo_ubi,
                3 => $oUbi->getDl(),
                4 => $oUbi->getRegion(),
            ];

            $cDirecciones = $oUbi->getDirecciones();
            if (!empty($cDirecciones)) {
                $oDireccion = $cDirecciones[0];
                $row[5] = $oDireccion->getDireccionVo()?->value() ?? '';
                $row[6] = $oDireccion->getC_p();
                $row[7] = $oDireccion->getPoblacion();
            } else {
                $row[5] = '';
                $row[6] = '';
                $row[7] = '';
            }
            $a_valores[] = $row;
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
        ];
    }
}
