<?php

namespace src\actividadtarifas\application;

use core\ConfigGlobal;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use web\TiposActividades;

/**
 * Data builder: listado de `TarifaUbi` por `id_ubi` + `year`.
 *
 * Sucesor de la rama `get` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TarifaUbiListaData
{
    /**
     * @return array{
     *   a_cabeceras: array<int,string>,
     *   a_valores: array<int,array<int,string|array{clase?:string,script?:string,valor?:string}>>,
     *   any_anterior: int,
     *   any_actual: int,
     *   puede_anadir: bool,
     *   id_ubi: int,
     *   year: int
     * }
     */
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $year = (int)($input['year'] ?? 0);

        $miSfsv = ConfigGlobal::mi_sfsv();
        $a_seccion = [1 => _("sv"), 2 => _("sf")];
        $aTipoSerie = SerieId::getArraySerie();

        $repoTarifa = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $repoRel = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $repoTipoTarifa = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);

        $cTarifas = [];
        if ($id_ubi !== 0 && $year !== 0) {
            $cTarifas = $repoTarifa->getTarifaUbis([
                'id_ubi' => $id_ubi,
                'year' => $year,
                '_ordre' => 'year,id_tarifa',
            ]);
        }

        $a_valores = [];
        $i = 0;
        if (is_array($cTarifas)) {
            foreach ($cTarifas as $oTarifaUbi) {
                $i++;
                $id_item = $oTarifaUbi->getId_item();
                $id_tarifa = $oTarifaUbi->getId_tarifa();
                $id_serie = $oTarifaUbi->getId_serie();
                $cantidad = $oTarifaUbi->getCantidad();

                $cantidad_txt = "$cantidad " . _("€");

                $cTipoActivTarifas = $repoRel->getTipoActivTarifas(['id_tarifa' => $id_tarifa]);
                $aplicado_a = '';
                $t = 0;
                if (is_array($cTipoActivTarifas)) {
                    foreach ($cTipoActivTarifas as $oRelacion) {
                        $t++;
                        $oTipoActividad = new TiposActividades($oRelacion->getId_tipo_activ());
                        if ($t > 1) {
                            $aplicado_a .= ', ';
                        }
                        $aplicado_a .= $oTipoActividad->getNomGral();
                    }
                }

                $oTipoTarifa = $repoTipoTarifa->findById($id_tarifa);
                $seccion = $oTipoTarifa !== null ? $oTipoTarifa->getSfsv() : 0;
                $letra = $oTipoTarifa !== null ? (string)$oTipoTarifa->getLetra() : '';
                $letra_serie = $letra . ' (' . ($aTipoSerie[$id_serie] ?? '') . ')';

                $a_valores[$i][1] = $a_seccion[$seccion] ?? '';
                if ($miSfsv === $seccion && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                    $a_valores[$i][2] = [
                        'script' => "fnjs_modificar($id_item,'$letra')",
                        'valor' => $letra_serie,
                    ];
                } else {
                    $a_valores[$i][2] = $letra_serie;
                }
                $a_valores[$i][3] = $aplicado_a;
                $a_valores[$i][4] = '0';
                $a_valores[$i][5] = ['clase' => 'derecha', 'valor' => $cantidad_txt];
                $a_valores[$i][6] = $oTipoTarifa !== null ? $oTipoTarifa->getModoTxt() : '';
            }
        }

        if (!empty($a_valores)) {
            $secc = [];
            $letr = [];
            foreach ($a_valores as $key => $row) {
                $secc[$key] = $row[1];
                $letr[$key] = is_array($row[2]) ? ($row[2]['valor'] ?? '') : $row[2];
            }
            array_multisort($secc, SORT_DESC, $letr, SORT_ASC, SORT_STRING, $a_valores);
        }

        $a_cabeceras = [
            _("sección"),
            _("tarifa"),
            _("se aplica a"),
            _("minimo"),
            _("precio"),
            _("método"),
        ];

        $puede_anadir = $id_ubi !== 0 && (
            $_SESSION['oPerm']->have_perm_oficina('adl')
            || $_SESSION['oPerm']->have_perm_oficina('pr')
            || $_SESSION['oPerm']->have_perm_oficina('calendario')
        );

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'any_anterior' => $year > 0 ? $year - 1 : 0,
            'any_actual' => $year,
            'puede_anadir' => (bool)$puede_anadir,
            'id_ubi' => $id_ubi,
            'year' => $year,
        ];
    }
}
